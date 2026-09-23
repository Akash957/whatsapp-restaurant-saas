@extends('layouts.auth')
@section('title','Register Restaurant')
@section('content')
<h4 class="fw-bold mb-1">Register your restaurant</h4>
<p class="text-muted small mb-4">Start your online ordering journey</p>
<form method="POST" action="{{ route('restaurant.register') }}">@csrf
    <div class="mb-2"><label class="form-label">Restaurant Name *</label><input name="restaurant_name" value="{{ old('restaurant_name') }}" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Owner Name *</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Email *</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Mobile *</label><input name="phone" value="{{ old('phone') }}" class="form-control" required placeholder="+9198..."></div>
    <div class="row g-2">
        <div class="col-6 mb-2"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-6 mb-2"><label class="form-label">Confirm *</label><input type="password" name="password_confirmation" class="form-control" required></div>
    </div>
    <div class="mb-2"><label class="form-label">Address *</label><input name="address" value="{{ old('address') }}" class="form-control" required></div>
    <div class="row g-2">
        <div class="col-6 mb-2"><label class="form-label">City *</label><input name="city" value="{{ old('city') }}" class="form-control" required></div>
        <div class="col-6 mb-2"><label class="form-label">State *</label><input name="state" value="{{ old('state') }}" class="form-control" required></div>
        <div class="col-6 mb-2"><label class="form-label">Country *</label><input name="country" value="{{ old('country','India') }}" class="form-control" required></div>
        <div class="col-6 mb-2"><label class="form-label">Postal Code *</label><input name="postal_code" value="{{ old('postal_code') }}" class="form-control" required></div>
    </div>
    <div class="mb-2"><label class="form-label">Restaurant Email</label><input type="email" name="restaurant_email" value="{{ old('restaurant_email') }}" class="form-control" placeholder="info@restaurant.com"></div>
    <div class="mb-3"><label class="form-label">Restaurant Phone</label><input name="restaurant_phone" value="{{ old('restaurant_phone') }}" class="form-control" placeholder="+9198..."></div>
    <button class="btn btn-primary w-100">Create Restaurant — Pending Approval</button>
</form>
<p class="text-center small mt-3 mb-0">Already have an account? <a href="{{ route('login') }}">Login</a></p>
@endsection