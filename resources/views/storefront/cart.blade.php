@extends('layouts.app')
@section('title','Cart - '.$restaurant->name)
@section('content')
<div class="container py-4">
<h3 class="fw-bold mb-3">{{ $restaurant->name }} — Cart</h3>
@if($cartError)<div class="alert alert-warning">{{ $cartError }}</div>@endif
@if(empty($items))<div class="text-center py-5"><i class="bi bi-cart fs-1 text-muted"></i><p class="text-muted mt-2">Your cart is empty</p><a href="{{ route('menu',$restaurant->slug) }}" class="btn btn-primary">Browse Menu</a></div>
@else
<div class="row g-4">
<div class="col-lg-8"><div class="card"><div class="card-body p-0"><table class="table mb-0"><thead class="table-light"><tr><th>Item</th><th>Qty</th><th>Total</th><th></th></tr></thead><tbody>
@foreach($items as $key=>$it)
@php $p = \App\Models\Product::find($it['product_id']); @endphp
<tr><td>{{ $p->name ?? 'Product #'.$it['product_id'] }} @if(!empty($it['variant_id']))<br><small class="text-muted">Variant #{{ $it['variant_id'] }}</small>@endif @if(!empty($it['addon_ids']))<br><small class="text-muted">Addons: {{ implode(', ', $it['addon_ids']) }}</small>@endif</td>
<td style="width:140px"><form method="POST" action="{{ route('cart.update',[$restaurant->slug,$key]) }}" class="d-flex gap-1">@csrf<input type="hidden" name="product_id" value="{{ $it['product_id'] }}"><input type="number" name="quantity" value="{{ $it['quantity'] }}" min="1" max="50" class="form-control form-control-sm"><button class="btn btn-sm btn-outline-primary">Update</button></form></td>
<td>{{ $it['quantity'] }} ×</td><td><form method="POST" action="{{ route('cart.remove',[$restaurant->slug,$key]) }}">@csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
@endforeach
</tbody></table></div></div>
<div class="card mt-3"><div class="card-body"><form method="POST" action="{{ route('coupon.apply',$restaurant->slug) }}" class="row g-2">@csrf<div class="col"><input name="coupon_code" value="{{ $couponCode }}" class="form-control" placeholder="Coupon code"></div><div class="col-auto"><button class="btn btn-outline-primary">Apply</button></div><div class="col-auto"><button name="coupon_code" value="" class="btn btn-outline-secondary">Remove</button></div></form></div></div>
</div>
<div class="col-lg-4">
<div class="card"><div class="card-header fw-semibold">Order Summary</div><div class="card-body">
@if($quote)
<div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ \App\Support\Money::format($quote['subtotal']) }}</span></div>
<div class="d-flex justify-content-between"><span>Discount</span><span>-{{ \App\Support\Money::format($quote['discount']) }}</span></div>
<div class="d-flex justify-content-between"><span>Tax</span><span>{{ \App\Support\Money::format($quote['tax']) }}</span></div>
<div class="d-flex justify-content-between"><span>Delivery</span><span>{{ \App\Support\Money::format($quote['delivery_charge']) }}</span></div>
<div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2"><span>Total</span><span>{{ \App\Support\Money::format($quote['total']) }}</span></div>
<a href="{{ route('checkout',$restaurant->slug) }}" class="btn btn-primary w-100 mt-3">Proceed to Checkout</a>
@else
<p class="text-muted small">Add items to see totals.</p>
@endif
</div></div>
</div>
</div>
@endif
</div>
@endsection