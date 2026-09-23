<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Commerce\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function vendorOrders(Request $request): View {
        $orders = Order::where('restaurant_id', $request->user()->restaurant_id)->with('customer')->latest()->paginate(20);
        return view('panel.orders.index', compact('orders'));
    }
    public function staffOrders(Request $request): View { return $this->vendorOrders($request); }
    public function orderDetail(Request $request, Order $order): View {
        $this->authorize('view', $order);
        $order->load('items.variants','items.addons','history','customer','restaurant');
        return view('panel.orders.show', compact('order'));
    }
    public function updateStatus(Request $request, Order $order): RedirectResponse {
        $this->authorize('update', $order);
        $data = $request->validate(['status'=>'required|in:confirmed,preparing,ready,out_for_delivery,delivered,cancelled','notes'=>'nullable|string']);
        $this->orderService->transition($order, $data['status'], $request->user(), $data['notes'] ?? null);
        return back()->with('success','Order status updated to '.$data['status']);
    }
}