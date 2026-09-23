@extends('layouts.panel')
@section('page_title','QR Code')
@section('content')
<div class="card"><div class="card-body text-center">
<h5>{{ $restaurant->name }}</h5><p class="text-muted small">{{ url('/restaurant/'.$restaurant->slug) }}</p>
<div class="my-4 d-flex justify-content-center"><div class="border p-3 bg-white">
@php $url = url('/restaurant/'.$restaurant->slug); @endphp
<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($url) }}" alt="QR">
</div></div>
<p class="small text-muted">Scan to view menu</p>
<a href="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data={{ urlencode($url) }}" target="_blank" class="btn btn-primary"><i class="bi bi-download"></i> Download QR</a>
<button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Print</button>
</div></div>
@endsection