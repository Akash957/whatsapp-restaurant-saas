<?php

namespace App\Services\Commerce;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Restaurant;
use App\Support\Money;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CartService
{
    /** @return array{items: array, subtotal: int, discount: int, tax: int, delivery_charge: int, tips: int, total: int, coupon_id: ?int} */
    public function quote(Restaurant $restaurant, array $items, ?string $couponCode = null, string $orderType = 'delivery', int $tips = 0, ?Customer $customer = null): array
    {
        abort_unless($restaurant->status === 'active', 404);
        Validator::make(['items' => $items, 'tips' => $tips, 'order_type' => $orderType], [
            'items' => ['array', 'max:100'],
            'items.*' => ['array'],
            'items.*.product_id' => ['required', 'integer', 'min:1'],
            'items.*.variant_id' => ['nullable', 'integer', 'min:1'],
            'items.*.addon_ids' => ['sometimes', 'array', 'max:100'],
            'items.*.addon_ids.*' => ['integer', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'between:1,50'],
            'tips' => ['integer', 'between:0,100000000'],
            'order_type' => ['required', 'in:delivery,pickup,dine_in'],
        ])->validate();

        $settings = $restaurant->deliverySetting;
        if (! $settings || ! $settings->{'enable_'.$orderType}) {
            $this->invalid('order_type', 'This order type is not available at this restaurant.');
        }
        if ($customer && (int) $customer->restaurant_id !== (int) $restaurant->id) {
            $this->invalid('customer', 'This customer does not belong to this restaurant.');
        }

        $products = Product::query()->where('restaurant_id', $restaurant->id)
            ->whereIn('id', array_column($items, 'product_id'))
            ->with(['category', 'variants', 'addons.items'])->get()->keyBy('id');
        $snapshots = [];
        $subtotal = 0;
        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (! $product || ! $product->is_available || ! $product->in_stock || ! $product->category
                || ! $product->category->is_active || (int) $product->category->restaurant_id !== (int) $restaurant->id) {
                $this->invalid('items', 'An item is no longer available. Remove it from your cart to continue.');
            }
            $variant = null;
            $base = (int) ($product->discount_price ?? $product->price);
            if (! empty($item['variant_id'])) {
                $selected = $product->variants->firstWhere('id', (int) $item['variant_id']);
                if (! $selected || ! $selected->is_available || (int) $selected->restaurant_id !== (int) $restaurant->id) {
                    $this->invalid('items', 'The selected variant is not available for this product.');
                }
                $base = (int) $selected->price;
                $variant = ['id' => $selected->id, 'name' => $selected->name, 'price' => $base];
            }
            $addonIds = array_map('intval', $item['addon_ids'] ?? []);
            if (count($addonIds) !== count(array_unique($addonIds))) {
                $this->invalid('items', 'Select each add-on only once.');
            }
            $addons = [];
            foreach ($product->addons as $group) {
                if ((int) $group->restaurant_id !== (int) $restaurant->id) {
                    continue;
                }
                $selected = $group->items->whereIn('id', $addonIds);
                if (($group->is_required && $selected->isEmpty())
                    || ((int) $group->max_selections > 0 && $selected->count() > (int) $group->max_selections)) {
                    $this->invalid('items', 'Check the required selections and selection limit for '.$group->name.'.');
                }
                foreach ($selected as $addon) {
                    if (! $addon->is_available || (int) $addon->restaurant_id !== (int) $restaurant->id) {
                        $this->invalid('items', 'A selected add-on is unavailable.');
                    }
                    $addons[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => (int) $addon->price];
                }
            }
            if (count($addons) !== count($addonIds)) {
                $this->invalid('items', 'An add-on does not belong to this product.');
            }
            $unitPrice = $base + array_sum(array_column($addons, 'price'));
            if ($base < 0 || $unitPrice < 0 || collect($addons)->contains(fn (array $addon): bool => $addon['price'] < 0)) {
                $this->invalid('items', 'This product has an invalid price. Please contact the restaurant.');
            }
            $quantity = (int) $item['quantity'];
            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;
            $snapshots[] = [
                'line_key' => $this->lineKey($item), 'product_id' => $product->id, 'name' => $product->name,
                'variant' => $variant, 'addons' => $addons, 'quantity' => $quantity,
                'unit_price' => $unitPrice, 'total' => $lineTotal,
            ];
        }

        $coupon = null;
        $discount = 0;
        if (filled($couponCode)) {
            $coupon = Coupon::query()->where('restaurant_id', $restaurant->id)
                ->where('code', strtoupper(trim($couponCode)))->first();
            if (! $coupon || ! $coupon->is_active
                || ($coupon->starts_at && $coupon->starts_at->isFuture())
                || ($coupon->ends_at && ! $coupon->ends_at->isFuture())
                || $subtotal < (int) $coupon->min_order) {
                $this->invalid('coupon_code', 'This coupon is invalid, expired, or its minimum order has not been reached.');
            }
            $usages = CouponUsage::query()->where('restaurant_id', $restaurant->id)->where('coupon_id', $coupon->id);
            if ($coupon->usage_limit !== null && (clone $usages)->count() >= (int) $coupon->usage_limit) {
                $this->invalid('coupon_code', 'This coupon has reached its usage limit.');
            }
            if ($customer && $coupon->per_customer_limit !== null
                && (clone $usages)->where('customer_id', $customer->id)->count() >= (int) $coupon->per_customer_limit) {
                $this->invalid('coupon_code', 'You have already used this coupon the maximum number of times.');
            }
            if (! in_array($coupon->type, ['fixed', 'percentage'], true)) {
                $this->invalid('coupon_code', 'This coupon cannot be applied.');
            }
            $discount = $coupon->type === 'percentage'
                ? $this->percentage($subtotal, (string) $coupon->value)
                : (int) $coupon->value;
            if ($coupon->max_discount !== null) {
                $discount = min($discount, (int) $coupon->max_discount);
            }
            $discount = max(0, min($subtotal, $discount));
        }
        $tax = $this->percentage($subtotal - $discount, (string) ($settings->tax_rate ?? '0'));
        $delivery = $orderType === 'delivery' && $subtotal > 0 ? (int) $settings->delivery_charge : 0;
        if ($settings->free_delivery_above !== null && $subtotal >= (int) $settings->free_delivery_above) {
            $delivery = 0;
        }

        return [
            'items' => $snapshots, 'subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax,
            'delivery_charge' => $delivery, 'tips' => $tips,
            'total' => $subtotal - $discount + $tax + $delivery + $tips, 'coupon_id' => $coupon?->id,
        ];
    }

    public function lineKey(array $item): string
    {
        $addons = array_map('intval', $item['addon_ids'] ?? []);
        sort($addons, SORT_NUMERIC);

        return hash('sha256', json_encode([(int) $item['product_id'], empty($item['variant_id']) ? null : (int) $item['variant_id'], $addons], JSON_THROW_ON_ERROR));
    }

    public function items(Restaurant $restaurant): array
    {
        return session()->get('cart.'.$restaurant->id.'.items', []);
    }

    public function couponCode(Restaurant $restaurant): ?string
    {
        return session()->get('cart.'.$restaurant->id.'.coupon_code');
    }

    public function defaultOrderType(Restaurant $restaurant): string
    {
        foreach (['delivery', 'pickup', 'dine_in'] as $type) {
            if ($restaurant->deliverySetting?->{'enable_'.$type}) {
                return $type;
            }
        }

        return 'delivery';
    }

    public function save(Restaurant $restaurant, array $items, ?string $couponCode = null): void
    {
        Cart::query()->updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'session_id' => session()->getId()],
            ['user_id' => auth()->id(), 'mobile' => auth()->user()?->phone, 'items' => array_values($items),
                'coupon_code' => $couponCode, 'last_activity_at' => now(), 'reminded_at' => null],
        );
        session()->put('cart.'.$restaurant->id, ['items' => $items, 'coupon_code' => $couponCode]);
    }

    public function clear(Restaurant $restaurant): void
    {
        Cart::query()->where('restaurant_id', $restaurant->id)->where('session_id', session()->getId())->delete();
        session()->forget('cart.'.$restaurant->id);
    }

    private function percentage(int $amount, string $rate): int
    {
        $basisPoints = Money::parse($rate);
        if ($basisPoints < 0 || $basisPoints > 10000) {
            $this->invalid('pricing', 'A percentage must be between zero and one hundred.');
        }

        return intdiv($amount * $basisPoints + 5000, 10000);
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
