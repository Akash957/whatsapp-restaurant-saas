@extends('layouts.app')
@section('title','About - '.$restaurant->name)
@section('content')
<div class="container py-5 text-center"><h2 class="fw-bold">{{ $restaurant->name }}</h2><p class="text-muted mx-auto" style="max-width:600px">{{ $restaurant->description ?? 'We serve delicious food made with love.' }}</p><p class="small"><i class="bi bi-geo-alt"></i> {{ $restaurant->address }}, {{ $restaurant->city }} · {{ $restaurant->phone }}<br>
@if($restaurant->email)<i class="bi bi-envelope"></i> {{ $restaurant->email }}@endif</p>
@if($restaurant->facebook || $restaurant->instagram)<p>@if($restaurant->facebook)<a href="{{ $restaurant->facebook }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-facebook"></i> Facebook</a>@endif @if($restaurant->instagram)<a href="{{ $restaurant->instagram }}" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-instagram"></i> Instagram</a>@endif</p>@endif
</div>
@endsection