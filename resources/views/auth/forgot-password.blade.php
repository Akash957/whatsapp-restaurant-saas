@extends('layouts.auth')
@section('title','Forgot Password')
@section('content')
<h4 class="fw-bold mb-1">Forgot password?</h4>
<p class="text-muted small mb-4">We'll send a reset link to your email</p>
<form method="POST" action="{{ route('password.email') }}">@csrf
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
    <button class="btn btn-primary w-100">Send Reset Link</button>
</form>
<p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}">Back to login</a></p>
@endsection