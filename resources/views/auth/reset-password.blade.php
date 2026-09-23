@extends('layouts.auth')
@section('title','Reset Password')
@section('content')
<h4 class="fw-bold mb-1">Reset password</h4>
<form method="POST" action="{{ route('password.update') }}">@csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $email ?? '') }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
    <button class="btn btn-primary w-100">Reset Password</button>
</form>
@endsection