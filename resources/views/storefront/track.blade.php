@extends('layouts.app')
@section('title','Track Order '.$order->order_number)
@section('content')
<div class="container py-4" style="max-width:800px">
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center"><span class="fw-semibold">Order #{{ $order->order_number }}</span><span class="badge bg-primary">{{ $order->status }}</span></div>
<div class="card-body">
<p><strong>Customer:</strong> {{ $order->customer_name }} ({{ $order->customer_mobile }})</p>
<p><strong>Type:</strong> {{ $order->order_type }} | <strong>Payment:</strong> {{ $order->payment_method }} ({{ $order->payment_status }})</p>
@if($order->address)<p><strong>Address:</strong> {{ $order->address }}, {{ $order->postal_code }}</p>@endif
<hr>
<h6>Timeline</h6>
<div class="d-flex flex-column gap-2">
@php $steps=['pending'=>'Order Placed','confirmed'=>'Confirmed','preparing'=>'Preparing','ready'=>'Ready','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered']; @endphp
@foreach($steps as $k=>$label)
<div class="d-flex align-items-center gap-3 @if($order->status===$k) fw-bold text-primary @elseif(array_search($k,array_keys($steps)) < array_search($order->status,array_keys($steps))) text-success @else text-muted @endif">
<span class="d-flex align-items-center justify-content-center rounded-circle border" style="width:28px;height:28px">@if(array_search($k,array_keys($steps)) < array_search($order->status,array_keys($steps)))<i class="bi bi-check"></i>@else<i class="bi bi-circle"></i>@endif</span> {{ $label }}
</div>
@endforeach
@if($order->status==='cancelled')<div class="text-danger fw-bold mt-2"><i class="bi bi-x-circle"></i> Cancelled</div>@endif
</div>
<hr>
<h6>Items</h6>
<table class="table table-sm"><thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead><tbody>@foreach($order->items as $it)<tr><td>{{ $it->name }}</td><td>{{ $it->quantity }}</td><td>{{ \App\Support\Money::format($it->total) }}</td></tr>@endforeach</tbody></table>
<hr>
<div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ \App\Support\Money::format($order->subtotal) }}</span></div>
<div class="d-flex justify-content-between"><span>Discount</span><span>-{{ \App\Support\Money::format($order->discount) }}</span></div>
<div class="d-flex justify-content-between"><span>Tax</span><span>{{ \App\Support\Money::format($order->tax) }}</span></div>
<div class="d-flex justify-content-between"><span>Delivery</span><span>{{ \App\Support\Money::format($order->delivery_charge) }}</span></div>
<div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2"><span>Total</span><span>{{ \App\Support\Money::format($order->total) }}</span></div>
</div>
</div>
</div>
@endsection