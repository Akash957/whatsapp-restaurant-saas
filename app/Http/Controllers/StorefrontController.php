<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Services\Commerce\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StorefrontController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function home(Restaurant $restaurant): View
    {
        $this->active($restaurant);
        $products = $this->catalog($restaurant)->orderByDesc('is_featured')->orderByDesc('is_popular')->orderBy('sort_order')->limit(8)->get();
        $categories = $restaurant->categories()->where('is_active', true)->orderBy('sort_order')->get();

        return view('storefront.home', compact('restaurant', 'products', 'categories'));
    }

    public function menu(Request $request, Restaurant $restaurant): View
    {
        $this->active($restaurant);
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'integer', 'min:1']]);
        $categories = $restaurant->categories()->where('is_active', true)->orderBy('sort_order')->get();
        $products = $this->catalog($restaurant)
            ->when($filters['category'] ?? null, fn (Builder $query, mixed $category): Builder => $query->where('category_id', $category))
            ->when($filters['q'] ?? null, fn (Builder $query, string $search): Builder => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')->paginate(12)->withQueryString();

        return view('storefront.menu', compact('restaurant', 'products', 'categories'));
    }

    public function product(Restaurant $restaurant, string $product): View
    {
        $this->active($restaurant);
        $product = $this->catalog($restaurant)->where('slug', $product)->with([
            'variants' => fn (Builder $query): Builder => $query->where('restaurant_id', $restaurant->id)->where('is_available', true),
            'addons' => fn (Builder $query): Builder => $query->where('restaurant_id', $restaurant->id),
            'addons.items' => fn (Builder $query): Builder => $query->where('restaurant_id', $restaurant->id)->where('is_available', true),
        ])->firstOrFail();

        return view('storefront.product', compact('restaurant', 'product'));
    }

    public function about(Restaurant $restaurant): View
    {
        $this->active($restaurant);

        return view('storefront.about', compact('restaurant'));
    }

    public function contact(Restaurant $restaurant): View
    {
        $this->active($restaurant);
        $restaurant->load('businessHours');

        return view('storefront.contact', compact('restaurant'));
    }

    public function cart(Restaurant $restaurant): View
    {
        $this->active($restaurant);
        $items = $this->cartService->items($restaurant);
        $couponCode = $this->cartService->couponCode($restaurant);
        $quote = null;
        $cartError = null;
        try {
            $quote = $this->cartService->quote($restaurant, $items, $couponCode, $this->cartService->defaultOrderType($restaurant), customer: $this->customer($restaurant));
        } catch (ValidationException $exception) {
            $cartError = $exception->validator->errors()->first();
        }

        return view('storefront.cart', compact('restaurant', 'items', 'couponCode', 'quote', 'cartError'));
    }

    public function add(CartItemRequest $request, Restaurant $restaurant): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $item = ['product_id' => (int) $data['product_id'], 'variant_id' => empty($data['variant_id']) ? null : (int) $data['variant_id'],
            'addon_ids' => array_map('intval', $data['addon_ids'] ?? []), 'quantity' => (int) $data['quantity']];
        $items = $this->cartService->items($restaurant);
        $key = $this->cartService->lineKey($item);
        $item['quantity'] += $items[$key]['quantity'] ?? 0;
        $items[$key] = $item;

        return $this->mutate($request, $restaurant, $items, $this->cartService->couponCode($restaurant), 'Item added to your cart.');
    }

    public function update(CartItemRequest $request, Restaurant $restaurant, string $line): RedirectResponse|JsonResponse
    {
        $items = $this->cartService->items($restaurant);
        abort_unless(isset($items[$line]), 404);
        $items[$line]['quantity'] = (int) $request->validated('quantity');

        return $this->mutate($request, $restaurant, $items, $this->cartService->couponCode($restaurant), 'Cart updated.');
    }

    public function remove(CartItemRequest $request, Restaurant $restaurant, string $line): RedirectResponse|JsonResponse
    {
        $items = $this->cartService->items($restaurant);
        abort_unless(isset($items[$line]), 404);
        unset($items[$line]);
        $this->cartService->save($restaurant, $items, null);

        return $this->respond($request, $restaurant, 'Item removed. Reapply your coupon if needed.', ['items' => array_values($items)]);
    }

    public function coupon(CartItemRequest $request, Restaurant $restaurant): RedirectResponse|JsonResponse
    {
        $code = $request->validated('coupon_code');
        $code = filled($code) ? strtoupper(trim($code)) : null;
        if ($code === null) {
            $this->cartService->save($restaurant, $this->cartService->items($restaurant), null);

            return $this->respond($request, $restaurant, 'Coupon removed.', []);
        }

        return $this->mutate($request, $restaurant, $this->cartService->items($restaurant), $code, 'Coupon applied. Final eligibility is checked at checkout.');
    }

    private function mutate(CartItemRequest $request, Restaurant $restaurant, array $items, ?string $coupon, string $message): RedirectResponse|JsonResponse
    {
        $quote = $this->cartService->quote($restaurant, $items, $coupon, $this->cartService->defaultOrderType($restaurant), customer: $this->customer($restaurant));
        $this->cartService->save($restaurant, $items, $coupon);

        return $this->respond($request, $restaurant, $message, $quote);
    }

    private function respond(Request $request, Restaurant $restaurant, string $message, array $data): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
        }

        return to_route('store.cart', ['restaurant' => $restaurant->slug])->with('success', $message);
    }

    private function catalog(Restaurant $restaurant): Builder
    {
        return Product::query()->where('restaurant_id', $restaurant->id)->where('is_available', true)
            ->whereHas('category', fn (Builder $query): Builder => $query->where('restaurant_id', $restaurant->id)->where('is_active', true))
            ->with('category');
    }

    private function customer(Restaurant $restaurant): ?Customer
    {
        return auth()->check() ? Customer::query()->where('restaurant_id', $restaurant->id)->where('user_id', auth()->id())->first() : null;
    }

    public function success(Order $order): View
    {
        $order->load('items.variants','items.addons','restaurant');
        return view('storefront.track', compact('order'));
    }

    public function track(string $tracking_token): View
    {
        $order = \App\Models\Order::where('tracking_token', $tracking_token)->with('items.variants','items.addons','history','restaurant')->firstOrFail();
        return view('storefront.track', compact('order'));
    }

    private function active(Restaurant $restaurant): void
    {
        abort_unless($restaurant->status === 'active', 404);
    }
}
