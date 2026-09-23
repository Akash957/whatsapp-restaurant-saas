@extends('layouts.panel')
@section('page_title','Dashboard')
@section('content')
<div class="row g-3 mb-4">
    @foreach(['Today Orders'=>$stats['today_orders']??0,'Pending'=>$stats['pending_orders']??0,'Preparing'=>$stats['preparing_orders']??0,'Completed'=>$stats['completed_orders']??0,'Today Revenue'=>\App\Support\Money::format($stats['today_revenue']??0),'Customers'=>$stats['total_customers']??0] as $k=>$v)
    <div class="col-6 col-lg-3 col-xl-2"><div class="card stat-card"><div class="card-body"><div class="small text-muted">{{ $k }}</div><div class="h5 mb-0">{{ $v }}</div></div></div></div>
    @endforeach
</div>
<div class="card"><div class="card-header fw-semibold">Recent Orders</div><div class="card-body p-0"><table class="table mb-0"><thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead><tbody>@forelse($recentOrders??[] as $o)<tr><td><a href="{{ route('vendor.order.detail',$o->order_number) }}">{{ $o->order_number }}</a></td><td>{{ $o->customer_name }}</td><td>{{ \App\Support\Money::format($o->total) }}</td><td><span class="badge bg-primary">{{ $o->status }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">No orders yet</td></tr>@endforelse</tbody></table></div></div>
@if(($restaurant->status ?? '')==='pending')<div class="alert alert-warning mt-3">Your restaurant is pending admin approval. You can still set up categories, products, and business hours.</div>@endif
@endsection