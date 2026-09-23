<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dashboard(Request $request): View {
        $orders = Order::where('user_id', $request->user()->id)->latest()->limit(5)->get();
        return view('customer.dashboard', compact('orders'));
    }
    public function orders(Request $request): View {
        $orders = Order::where('user_id', $request->user()->id)->latest()->paginate(20);
        return view('customer.dashboard', compact('orders'));
    }
    public function orderDetail(Request $request, Order $order): View {
        $this->authorize('view', $order);
        $order->load('items.variants','items.addons','history');
        return view('customer.order', compact('order'));
    }
    public function profile(Request $request): View { return view('customer.dashboard', ['user'=>$request->user()]); }
    public function updateProfile(Request $request): RedirectResponse {
        $data = $request->validate(['name'=>'required|string|max:255','phone'=>'nullable|string|max:30']);
        $request->user()->update($data);
        return back()->with('success','Profile updated.');
    }
}