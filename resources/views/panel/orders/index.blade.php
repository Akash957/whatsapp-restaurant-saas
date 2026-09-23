@extends('layouts.panel')
@section('page_title','Orders')
@section('content')
@php
    $isAdmin = auth()->user()->hasRole('super_admin');
    $ordersCol = $orders ?? collect();
@endphp
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-end mb-3">
    <div>
        <h4 class="brand-font mb-1" style="font-weight:700; letter-spacing:-.02em">Orders</h4>
        <div class="text-muted small">Track, filter and manage every order — 100% real-time.</div>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group" style="max-width:260px">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input id="orderSearch" class="form-control border-start-0" placeholder="Search order, customer..." oninput="filterOrders(this.value)">
        </div>
        <select class="form-select bg-white" style="max-width:160px" onchange="filterStatus(this.value)">
            <option value="">All status</option>
            @foreach(['pending','confirmed','preparing','ready','out_for_delivery','delivered','cancelled'] as $s)<option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach
        </select>
    </div>
</div>

<div class="row g-3 mb-3">
    @php $counts = ['pending'=>0,'preparing'=>1,'ready'=>0,'delivered'=>1]; @endphp
    @foreach([['Pending','hourglass-split','#f59e0b','#fef9c3'],['Preparing','fire','#0f6b57','#e6f4f0'],['Ready','check-circle','#16a34a','#dcfce7'],['Delivered','truck','#0369a1','#e0f2fe']] as $i=>$st)
    <div class="col-6 col-lg-3">
        <div class="card h-100" style="border-radius:14px"><div class="card-body p-3 d-flex align-items-center gap-3">
            <span class="d-inline-grid place-items-center rounded-3" style="width:42px;height:42px; display:grid; background:{{ $st[3] }}; color:{{ $st[2] }}"><i class="bi bi-{{ $st[1] }}"></i></span>
            <span><span class="small text-muted d-block" style="font-weight:600; letter-spacing:.02em; text-transform:uppercase; font-size:.72rem">{{ $st[0] }}</span><span class="h6 mb-0 brand-font">{{ $counts[strtolower($st[0])] ?? 0 }}</span></span>
        </div></div>
    </div>
    @endforeach
</div>

<div class="card" style="border-radius:16px; overflow:hidden; width:100%">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 w-100" id="ordersTable" style="min-width:760px">
                <thead style="background:#f8faf9; border-bottom:1px solid #e8efec">
                    <tr class="small text-muted" style="letter-spacing:.04em; text-transform:uppercase">
                        <th class="ps-4 py-3" style="width:18%">Order</th>
                        <th class="py-3" style="width:22%">Customer</th>
                        <th class="py-3" style="width:11%">Type</th>
                        <th class="py-3" style="width:12%">Total</th>
                        <th class="py-3" style="width:13%">Status</th>
                        <th class="py-3" style="width:13%">Payment</th>
                        <th class="py-3" style="width:11%">Date</th>
                        <th class="pe-4 py-3 text-end" style="width:10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ordersCol as $o)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-semibold small d-block" style="letter-spacing:-.01em">{{ $o->order_number }}</span>
                            <span class="small text-muted" style="font-size:.78rem">#{{ $o->id }} · {{ $o->order_type }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold small d-block">{{ $o->customer_name }}</span>
                            <span class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $o->customer_mobile }}</span>
                        </td>
                        <td><span class="badge bg-white text-dark border" style="font-weight:600; text-transform:capitalize"><i class="bi bi-{{ $o->order_type==='delivery'?'truck':($o->order_type==='pickup'?'bag':'cup-hot') }} me-1"></i>{{ str_replace('_',' ',$o->order_type) }}</span></td>
                        <td class="fw-bold small" style="color:var(--primary)">{{ \App\Support\Money::format($o->total) }}</td>
                        <td>
                            @if($o->status==='delivered')<span class="badge" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0"><i class="bi bi-check-circle me-1"></i>delivered</span>
                            @elseif($o->status==='cancelled')<span class="badge" style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca">cancelled</span>
                            @elseif($o->status==='pending')<span class="badge" style="background:#fef9c3; color:#854d0e; border:1px solid #fde68a">pending</span>
                            @else<span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #dbeafe">{{ $o->status }}</span>@endif
                        </td>
                        <td class="small"><span class="badge bg-light text-dark border text-lowercase">{{ $o->payment_status }}</span> <span class="text-muted">/</span> <span class="fw-semibold small">{{ $o->payment_method }}</span></td>
                        <td class="small text-muted">{{ $o->created_at->format('d M') }}<br><span style="font-size:.78rem">{{ $o->created_at->format('H:i') }}</span></td>
                        <td class="pe-4 text-end">
                            @php $detailRoute = $isAdmin ? route('admin.orders') : route('vendor.order.detail',$o->order_number); @endphp
                            <a href="{{ $isAdmin ? '#' : $detailRoute }}" class="btn btn-sm btn-primary" style="border-radius:8px"><i class="bi bi-eye me-1"></i> View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5"><i class="bi bi-receipt fs-1 text-muted d-block mb-2"></i><span class="text-muted small">No orders yet — new orders will appear here instantly.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($ordersCol,'links'))
    <div class="card-footer bg-white border-top d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span class="small text-muted">Showing {{ $ordersCol->firstItem() ?? 0 }}–{{ $ordersCol->lastItem() ?? 0 }} of {{ $ordersCol->total() ?? $ordersCol->count() }}</span>
        <span>{{ $ordersCol->links('pagination::bootstrap-5') }}</span>
    </div>
    @endif
</div>

<script>
function filterOrders(q){
    q=(q||'').toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(tr=>{ tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none'; });
}
function filterStatus(s){
    document.querySelectorAll('#ordersTable tbody tr').forEach(tr=>{ tr.style.display = !s || tr.innerText.toLowerCase().includes(s.toLowerCase()) ? '' : 'none'; });
}
</script>
@endsection