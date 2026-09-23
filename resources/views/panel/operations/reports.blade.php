@extends('layouts.panel')
@section('page_title','Reports')
@section('content')
<div class="row g-3">
<div class="col-md-4"><div class="card"><div class="card-body"><h6>Revenue</h6><p class="small text-muted">Daily / Weekly / Monthly charts via Chart.js — data from orders table aggregated by date.</p><canvas id="revChart"></canvas></div></div></div>
<div class="col-md-4"><div class="card"><div class="card-body"><h6>Orders</h6><p class="small text-muted">Order status breakdown</p><canvas id="ordChart"></canvas></div></div></div>
<div class="col-md-4"><div class="card"><div class="card-body"><h6>Export</h6><button class="btn btn-outline-primary w-100">Export CSV</button><p class="small text-muted mt-2">Filters: date range, restaurant, payment/status</p></div></div></div>
</div>
@endsection