<?php

namespace App\Services\Commerce;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\OrderItemVariant;
use App\Models\OrderStatusHistory;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private CartService $cart) {}

    public function create(Restaurant $restaurant, array $data, array $items, ?User $user = null): Order
    {
        Validator::make($data, [
            'idempotency_key' => ['required', 'uuid'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_mobile' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'payment_method' => ['required', 'in:cod,razorpay'],
            'order_type' => ['required', 'in:delivery,pickup,dine_in'],
            'address' => ['required_if:order_type,delivery', 'nullable', 'string', 'max:1000'],
            'building' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required_if:order_type,delivery', 'nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'tips' => ['sometimes', 'integer', 'between:0,100000000'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ])->validate();
        if ($user && ! $user->is_active) {
            throw ValidationException::withMessages(['customer' => 'This account is inactive.']);
        }
        if ($data['payment_method'] === 'razorpay' && (! config('services.razorpay.key') || ! config('services.razorpay.secret'))) {
            throw ValidationException::withMessages(['payment_method' => 'Online payments are not configured. Please choose cash on delivery.']);
        }

        return DB::transaction(function () use ($restaurant, $data, $items, $user): Order {
            $restaurant = Restaurant::query()->whereKey($restaurant->id)->lockForUpdate()->firstOrFail();
            abort_unless($restaurant->status === 'active', 404);
            $existing = Order::query()->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                if ((int) $existing->restaurant_id !== (int) $restaurant->id
                    || $existing->customer_mobile !== $data['customer_mobile']
                    || (string) $existing->user_id !== (string) $user?->id) {
                    throw ValidationException::withMessages(['idempotency_key' => 'This checkout key cannot be used. Start a new checkout.']);
                }

                return $existing->load(['items.variants', 'items.addons', 'history', 'restaurant']);
            }
            $subscription = Subscription::query()->where('restaurant_id', $restaurant->id)
                ->whereIn('status', ['active', 'trial'])->where('starts_at', '<=', now())
                ->where('ends_at', '>', now())->with('plan')->latest('ends_at')->lockForUpdate()->first();
            if (! $subscription || ! $subscription->plan) {
                throw ValidationException::withMessages(['restaurant' => 'This restaurant is not accepting new orders at the moment.']);
            }
            $limit = $subscription->plan->max_orders;
            if ($limit !== null && (int) $limit >= 0
                && Order::query()->where('restaurant_id', $restaurant->id)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->where('created_at', '<', now()->startOfMonth()->addMonth())->count() >= (int) $limit) {
                throw ValidationException::withMessages(['restaurant' => 'This restaurant has reached its monthly order limit. Please contact the restaurant.']);
            }
            if (empty($items)) {
                throw ValidationException::withMessages(['items' => 'Your cart is empty.']);
            }
            $customer = Customer::query()->firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'mobile' => $data['customer_mobile']],
                ['user_id' => $user?->id, 'name' => $data['customer_name'], 'email' => $data['customer_email'] ?? null, 'is_active' => true],
            );
            if (! $customer->is_active) {
                throw ValidationException::withMessages(['customer_mobile' => 'This customer cannot place orders. Please contact the restaurant.']);
            }
            if ($user && (int) $customer->user_id === (int) $user->id) {
                $customer->update(['name' => $data['customer_name'], 'email' => $data['customer_email'] ?? $customer->email]);
            }
            if (filled($data['coupon_code'] ?? null)) {
                Coupon::query()->where('restaurant_id', $restaurant->id)
                    ->where('code', strtoupper(trim($data['coupon_code'])))->lockForUpdate()->first();
            }
            $quote = $this->cart->quote($restaurant, $items, $data['coupon_code'] ?? null, $data['order_type'], (int) ($data['tips'] ?? 0), $customer);
            if ($quote['subtotal'] < (int) $restaurant->deliverySetting->minimum_order) {
                throw ValidationException::withMessages(['items' => 'The restaurant minimum order is '.\App\Support\Money::format((int) $restaurant->deliverySetting->minimum_order).'.']);
            }
            $order = Order::query()->create(array_merge(
                Arr::only($data, ['customer_name', 'customer_mobile', 'customer_email', 'address', 'building', 'landmark', 'postal_code', 'notes', 'order_type', 'payment_method', 'idempotency_key']),
                Arr::except($quote, ['items']),
                ['restaurant_id' => $restaurant->id, 'customer_id' => $customer->id, 'user_id' => $user?->id,
                    'order_number' => (string) Str::uuid(), 'tracking_token' => (string) Str::uuid(),
                    'status' => 'pending', 'payment_status' => 'pending'],
            ));
            $order->update(['order_number' => 'ORD-'.now()->year.'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);
            foreach ($quote['items'] as $snapshot) {
                $item = OrderItem::query()->create(array_merge(
                    Arr::only($snapshot, ['product_id', 'name', 'quantity', 'unit_price', 'total']),
                    ['restaurant_id' => $restaurant->id, 'order_id' => $order->id],
                ));
                if ($snapshot['variant']) {
                    OrderItemVariant::query()->create([
                        'restaurant_id' => $restaurant->id, 'order_item_id' => $item->id,
                        'product_variant_id' => $snapshot['variant']['id'], 'name' => $snapshot['variant']['name'], 'price' => $snapshot['variant']['price'],
                    ]);
                }
                foreach ($snapshot['addons'] as $addon) {
                    OrderItemAddon::query()->create([
                        'restaurant_id' => $restaurant->id, 'order_item_id' => $item->id,
                        'product_addon_item_id' => $addon['id'], 'name' => $addon['name'], 'price' => $addon['price'],
                    ]);
                }
            }
            OrderStatusHistory::query()->create(['restaurant_id' => $restaurant->id, 'order_id' => $order->id, 'user_id' => $user?->id, 'status' => 'pending', 'notes' => 'Order placed.']);
            if ($quote['coupon_id']) {
                CouponUsage::query()->create(['restaurant_id' => $restaurant->id, 'coupon_id' => $quote['coupon_id'], 'customer_id' => $customer->id, 'order_id' => $order->id, 'discount' => $quote['discount']]);
            }
            OrderPlaced::dispatch($order);

            return $order->load(['items.variants', 'items.addons', 'history', 'restaurant']);
        }, 3);
    }

    public function transition(Order $order, string $status, ?User $actor = null, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $status, $actor, $notes): Order {
            $order = Order::query()->where('restaurant_id', $order->restaurant_id)->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $previousStatus = $order->status;
            if ($previousStatus === $status) {
                return $order;
            }
            $allowed = [
                'pending' => ['confirmed', 'cancelled'],
                'confirmed' => ['preparing', 'cancelled'],
                'preparing' => ['ready', 'cancelled'],
                'ready' => [$order->order_type === 'delivery' ? 'out_for_delivery' : 'delivered'],
                'out_for_delivery' => ['delivered'],
            ];
            if (! in_array($status, $allowed[$previousStatus] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'This order status transition is not allowed.']);
            }
            $order->update(['status' => $status]);
            OrderStatusHistory::query()->create([
                'restaurant_id' => $order->restaurant_id, 'order_id' => $order->id, 'user_id' => $actor?->id,
                'status' => $status, 'notes' => $notes,
            ]);
            OrderStatusChanged::dispatch($order, $previousStatus);

            return $order->fresh(['history']);
        }, 3);
    }
}
