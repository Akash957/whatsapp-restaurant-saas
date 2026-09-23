@extends('layouts.panel')
@section('page_title','My Account')
@section('content')
<div class="row g-3">
<div class="col-md-4"><div class="card"><div class="card-body text-center"><i class="bi bi-person-circle fs-1"></i><h5 class="mt-2">{{ auth()->user()->name }}</h5><p class="small text-muted mb-1">{{ auth()->user()->email }}</p><p class="small text-muted">{{ auth()->user()->phone }}</p><a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-primary">Edit Profile</a></div></div></div>
<div class="col-md-8"><div class="card"><div class="card-header fw-semibold">Recent Orders</div><div class="card-body p-0"><table class="table mb-0"><thead class="table-light"><tr><th>#</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody>
@forelse($orders ?? [] as $o)<tr><td>{{ $o->order_number }}</td><td>{{ \App\Support\Money::format($o->total) }}</td><td><span class="badge bg-primary">{{ $o->status }}</span></td><td class="small">{{ $o->created_at->format('d M Y') }}</td><td><a href="{{ route('customer.order.detail',$o->order_number) }}" class="btn btn-sm btn-outline-primary">View</a></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-3">No orders yet</td></tr>@endforelse
</tbody></table></div></div></div>
</div>
@endsection