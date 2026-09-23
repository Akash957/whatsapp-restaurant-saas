@extends('layouts.app')
@section('title','Contact - '.$restaurant->name)
@section('content')
<div class="container py-4">
<h3 class="fw-bold">{{ $restaurant->name }} — Contact</h3>
<div class="row g-4">
<div class="col-md-6"><div class="card"><div class="card-body">
<p><i class="bi bi-geo-alt"></i> {{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state }} {{ $restaurant->postal_code }}</p>
<p><i class="bi bi-telephone"></i> {{ $restaurant->phone }}<br>
@if($restaurant->whatsapp_number)<i class="bi bi-whatsapp text-success"></i> {{ $restaurant->whatsapp_number }}@endif<br>
<i class="bi bi-envelope"></i> {{ $restaurant->email }}</p>
</div></div></div>
<div class="col-md-6"><div class="card"><div class="card-header fw-semibold">Opening Hours</div><div class="card-body p-0"><table class="table mb-0"><tbody>
@php $days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
@foreach($restaurant->businessHours as $h)<tr><td>{{ $days[$h->day] }}</td><td>@if($h->is_closed)<span class="badge bg-danger">Closed</span>@else{{ \Carbon\Carbon::parse($h->opens_at)->format('g:i A') }} - {{ \Carbon\Carbon::parse($h->closes_at)->format('g:i A') }}@endif</td></tr>@endforeach
</tbody></table></div></div></div>
</div>
</div>
@endsection