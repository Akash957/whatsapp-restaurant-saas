@extends('layouts.auth')
@section('title','Login')
@section('content')
<h4 class="fw-bold mb-1">Welcome back</h4>
<p class="text-muted small mb-4">Sign in to your account</p>
<form method="POST" action="{{ url('/login') }}">@csrf
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <label class="form-check mb-0"><input type="checkbox" name="remember" class="form-check-input"> <span class="form-check-label small">Remember me</span></label>
        <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
    </div>
    <button class="btn btn-primary w-100">Sign In</button>
</form>
<p class="text-center small mt-3 mb-0">Don't have an account? <a href="{{ route('register') }}">Register restaurant</a> · <a href="{{ route('customer.register') }}">Customer signup</a></p>
@endsection