@extends('layouts.panel')
@section('page_title','Order '.$order->order_number)
@section('content')
<div class="card"><div class="card-header fw-semibold">Order #{{ $order->order_number }} <span class="badge bg-primary ms-2">{{ $order->status }}</span></div><div class="card-body">
<table class="table table-sm"><thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead><tbody>@foreach($order->items as $it)<tr><td>{{ $it->name }}</td><td>{{ $it->quantity }}</td><td>{{ \App\Support\Money::format($it->total) }}</td></tr>@endforeach</tbody></table>
<hr>
<div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ \App\Support\Money::format($order->subtotal) }}</span></div>
<div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2"><span>Total</span><span>{{ \App\Support\Money::format($order->total) }}</span></div>
<hr>
<h6>Timeline</h6>
@foreach($order->history as $h)<div class="d-flex gap-3 mb-2"><span class="badge bg-light text-dark border">{{ $h->status }}</span><span class="small text-muted">{{ $h->created_at->format('d M Y H:i') }}</span></div>@endforeach
</div></div>
@endsection