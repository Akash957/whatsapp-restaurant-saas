@extends('layouts.auth')
@section('title','Verify Email')
@section('content')
<h4 class="fw-bold mb-3">Verify your email</h4>
<p class="text-muted small">We sent a verification link to your email. Click it to verify your account. If you didn't receive it, request a new one.</p>
<form method="POST" action="{{ route('verification.send') }}">@csrf<button class="btn btn-primary w-100">Resend Verification Email</button></form>
<form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button class="btn btn-outline-secondary w-100">Logout</button></form>
@endsection