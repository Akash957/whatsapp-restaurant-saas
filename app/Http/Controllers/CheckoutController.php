<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Restaurant;
use App\Services\Commerce\CartService;
use App\Services\Commerce\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cartService, private OrderService $orderService) {}

    public function index(Restaurant $restaurant): View {
        abort_unless($restaurant->status==='active', 404);
        $items = $this->cartService->items($restaurant);
        $couponCode = $this->cartService->couponCode($restaurant);
        $quote = null;
        try { $quote = $this->cartService->quote($restaurant, $items, $couponCode, $this->cartService->defaultOrderType($restaurant)); } catch (\Exception $e) {}
        return view('storefront.checkout', compact('restaurant','items','couponCode','quote'));
    }

    public function store(CheckoutRequest $request, Restaurant $restaurant): RedirectResponse {
        abort_unless($restaurant->status==='active', 404);
        $data = $request->validated();
        $items = $this->cartService->items($restaurant);
        $data['idempotency_key'] = (string) Str::uuid();
        $data['coupon_code'] = $this->cartService->couponCode($restaurant);
        $order = $this->orderService->create($restaurant, $data, $items, $request->user());
        $this->cartService->clear($restaurant);
        return redirect()->route('order.track', $order->tracking_token)->with('success','Order placed! #'.$order->order_number);
    }
}