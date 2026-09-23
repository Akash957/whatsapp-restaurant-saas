@extends('layouts.auth')
@section('title','Customer Register')
@section('content')
<h4 class="fw-bold mb-1">Create customer account</h4>
<p class="text-muted small mb-4">Order from your favorite restaurants</p>
<form method="POST" action="{{ route('customer.register.post') }}">@csrf
    <div class="mb-3"><label class="form-label">Name *</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Mobile *</label><input name="phone" value="{{ old('phone') }}" class="form-control" required placeholder="+9198..."></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
    <button class="btn btn-primary w-100">Create Account</button>
</form>
<p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}">Already have an account? Login</a></p>
@endsection