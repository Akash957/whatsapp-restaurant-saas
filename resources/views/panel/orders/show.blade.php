@extends('layouts.panel')
@section('page_title','Order '.$order->order_number)
@section('content')
<div class="row g-3">
<div class="col-lg-8">
<div class="card mb-3"><div class="card-header fw-semibold">Order #{{ $order->order_number }} <span class="badge bg-primary ms-2">{{ $order->status }}</span></div><div class="card-body">
<p class="mb-1"><strong>Customer:</strong> {{ $order->customer_name }} ({{ $order->customer_mobile }})</p>
<p class="mb-1"><strong>Type:</strong> {{ $order->order_type }} | <strong>Payment:</strong> {{ $order->payment_method }} ({{ $order->payment_status }})</p>
@if($order->address)<p class="mb-1"><strong>Address:</strong> {{ $order->address }}, {{ $order->building }}, {{ $order->landmark }} - {{ $order->postal_code }}</p>@endif
@if($order->notes)<p class="mb-1"><strong>Notes:</strong> {{ $order->notes }}</p>@endif
<hr>
<h6>Items</h6>
<table class="table table-sm"><thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>
@foreach($order->items as $it)<tr><td>{{ $it->name }} @if($it->variants->count())<br><small class="text-muted">Variant: {{ $it->variants->pluck('name')->join(', ') }}</small>@endif @if($it->addons->count())<br><small class="text-muted">Addons: {{ $it->addons->pluck('name')->join(', ') }}</small>@endif</td><td>{{ $it->quantity }}</td><td>{{ \App\Support\Money::format($it->unit_price) }}</td><td>{{ \App\Support\Money::format($it->total) }}</td></tr>@endforeach
</tbody></table>
<hr>
<div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ \App\Support\Money::format($order->subtotal) }}</span></div>
<div class="d-flex justify-content-between"><span>Discount</span><span>-{{ \App\Support\Money::format($order->discount) }}</span></div>
<div class="d-flex justify-content-between"><span>Tax</span><span>{{ \App\Support\Money::format($order->tax) }}</span></div>
<div class="d-flex justify-content-between"><span>Delivery</span><span>{{ \App\Support\Money::format($order->delivery_charge) }}</span></div>
@if($order->tips)<div class="d-flex justify-content-between"><span>Tips</span><span>{{ \App\Support\Money::format($order->tips) }}</span></div>@endif
<div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2"><span>Total</span><span>{{ \App\Support\Money::format($order->total) }}</span></div>
</div></div>
<div class="card"><div class="card-header">Timeline</div><div class="card-body">@foreach($order->history as $h)<div class="d-flex gap-3 mb-2"><span class="badge bg-light text-dark border" style="min-width:90px">{{ $h->status }}</span><span class="small text-muted">{{ $h->created_at->format('d M Y H:i') }} @if($h->notes) — {{ $h->notes }} @endif</span></div>@endforeach</div></div>
</div>
<div class="col-lg-4">
<div class="card"><div class="card-header fw-semibold">Update Status</div><div class="card-body"><form method="POST" action="{{ route('vendor.order.status.update',$order->order_number) }}">@csrf @method('PUT')
<select name="status" class="form-select mb-2" required>
@foreach(['confirmed','preparing','ready','out_for_delivery','delivered','cancelled'] as $s)<option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach
</select>
<textarea name="notes" class="form-control mb-2" placeholder="Notes (optional)"></textarea>
<button class="btn btn-primary w-100">Update</button>
</form></div></div>
<div class="card mt-3"><div class="card-body text-center"><a href="{{ route('order.track',$order->tracking_token) }}" target="_blank" class="btn btn-outline-primary w-100"><i class="bi bi-box-arrow-up-right"></i> Track Order (Customer View)</a></div></div>
</div>
</div>
@endsection