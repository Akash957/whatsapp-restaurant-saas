@extends('layouts.app')
@section('title','Checkout - '.$restaurant->name)
@section('content')
<div class="container py-4">
<h3 class="fw-bold mb-3">Checkout — {{ $restaurant->name }}</h3>
<div class="row g-4">
<div class="col-lg-7">
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('checkout.store',$restaurant->slug) }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Name *</label><input name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Mobile *</label><input name="customer_mobile" value="{{ old('customer_mobile', auth()->user()->phone ?? '') }}" class="form-control" required placeholder="+9198..."></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Order Type *</label><select name="order_type" class="form-select" required><option value="delivery">Delivery</option><option value="pickup">Pickup</option><option value="dine_in">Dine-in</option></select></div>
<div class="col-12"><label class="form-label">Address *</label><input name="address" value="{{ old('address') }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Building</label><input name="building" value="{{ old('building') }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Landmark</label><input name="landmark" value="{{ old('landmark') }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Postal Code *</label><input name="postal_code" value="{{ old('postal_code') }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Payment *</label><select name="payment_method" class="form-select" required><option value="cod">Cash on Delivery</option><option value="razorpay">Online (Razorpay)</option></select></div>
<div class="col-md-6"><label class="form-label">Tips (paise)</label><input type="number" name="tips" value="{{ old('tips',0) }}" class="form-control" min="0"></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
</div>
<button class="btn btn-primary btn-lg w-100 mt-4">Place Order</button>
</form>
</div></div>
</div>
<div class="col-lg-5">
<div class="card"><div class="card-header fw-semibold">Summary</div><div class="card-body">
@if($quote)
<div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ \App\Support\Money::format($quote['subtotal']) }}</span></div>
<div class="d-flex justify-content-between"><span>Discount</span><span>-{{ \App\Support\Money::format($quote['discount']) }}</span></div>
<div class="d-flex justify-content-between"><span>Tax</span><span>{{ \App\Support\Money::format($quote['tax']) }}</span></div>
<div class="d-flex justify-content-between"><span>Delivery</span><span>{{ \App\Support\Money::format($quote['delivery_charge']) }}</span></div>
<div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2"><span>Total</span><span>{{ \App\Support\Money::format($quote['total']) }}</span></div>
@else
<p class="text-muted">Cart empty or invalid.</p>
@endif
</div></div>
</div>
</div>
</div>
@endsection