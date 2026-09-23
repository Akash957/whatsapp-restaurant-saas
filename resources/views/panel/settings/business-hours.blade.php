@extends('layouts.panel')
@section('page_title','Business Hours')
@section('content')
<div class="card"><div class="card-header fw-semibold">Business Hours</div><div class="card-body">
<form method="POST" action="{{ route('vendor.business-hours.update') }}">@csrf
@php $days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; $hours=$restaurant->businessHours->keyBy('day'); @endphp
<table class="table">
<thead><tr><th>Day</th><th>Closed</th><th>Opens At</th><th>Closes At</th></tr></thead>
<tbody>
@foreach(range(0,6) as $d)
@php $h=$hours->get($d); @endphp
<tr><td>{{ $days[$d] }}</td><td><input type="checkbox" name="hours[{{ $d }}][is_closed]" value="1" @checked($h->is_closed ?? false) class="form-check-input"></td><td><input type="time" name="hours[{{ $d }}][opens_at]" value="{{ $h->opens_at ? \Carbon\Carbon::parse($h->opens_at)->format('H:i') : '10:00' }}" class="form-control"></td><td><input type="time" name="hours[{{ $d }}][closes_at]" value="{{ $h->closes_at ? \Carbon\Carbon::parse($h->closes_at)->format('H:i') : '22:00' }}" class="form-control"></td></tr>
@endforeach
</tbody>
</table>
<button class="btn btn-primary">Save Hours</button>
</form>
</div></div>
@endsection