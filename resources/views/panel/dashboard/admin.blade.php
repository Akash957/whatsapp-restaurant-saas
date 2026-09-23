@extends('layouts.panel')
@section('page_title','Admin Dashboard')
@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <div>
        <h4 class="brand-font mb-1" style="font-weight:700; letter-spacing:-.02em">Welcome back, Super Admin</h4>
        <div class="text-muted small">Here's what's happening across your platform today.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.restaurants') }}" class="btn btn-primary"><i class="bi bi-shop me-1"></i> Restaurants</a>
        <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary bg-white"><i class="bi bi-bar-chart me-1"></i> Reports</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @php
        $cards = [
            ['label'=>'Total Restaurants','value'=>$stats['total_restaurants']??0,'icon'=>'bi-shop','bg'=>'#e6f4f0','color'=>'#0f6b57','sub'=>($stats['active_restaurants']??0).' active · '.($stats['pending_restaurants']??0).' pending'],
            ['label'=>'Total Customers','value'=>$stats['total_customers']??0,'icon'=>'bi-people','bg'=>'#e0f2fe','color'=>'#0369a1','sub'=>'Across all restaurants'],
            ['label'=>'Total Orders','value'=>$stats['total_orders']??0,'icon'=>'bi-receipt-cutoff','bg'=>'#fef9c3','color'=>'#a16207','sub'=>($stats['today_orders']??0).' today'],
            ['label'=>"Today's Revenue",'value'=>\App\Support\Money::format($stats['today_revenue']??0),'icon'=>'bi-currency-rupee','bg'=>'#dcfce7','color'=>'#166534','sub'=>'Monthly: '.\App\Support\Money::format($stats['monthly_revenue']??0)],
            ['label'=>'Active Subscriptions','value'=>$stats['active_subscriptions']??0,'icon'=>'bi-gem','bg'=>'#f3e8ff','color'=>'#7e22ce','sub'=>($stats['expired_subscriptions']??0).' expired'],
            ['label'=>'Pending Approvals','value'=>$stats['pending_restaurants']??0,'icon'=>'bi-hourglass-split','bg'=>'#ffedd5','color'=>'#9a3412','sub'=>'Needs action'],
        ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-xl-2 col-lg-4">
        <div class="card stat-card h-100" style="border-radius:16px">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="d-inline-grid place-items-center rounded-3" style="width:38px;height:38px; display:grid; background:{{ $c['bg'] }}; color:{{ $c['color'] }}"><i class="bi {{ $c['icon'] }}"></i></span>
                    <i class="bi bi-three-dots text-muted"></i>
                </div>
                <div class="small text-muted" style="font-weight:600; letter-spacing:.02em; font-size:.78rem; text-transform:uppercase">{{ $c['label'] }}</div>
                <div class="h5 mb-1 brand-font" style="font-weight:700; letter-spacing:-.02em">{{ $c['value'] }}</div>
                <div class="small text-muted" style="font-size:.78rem">{{ $c['sub'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card" style="border-radius:16px; overflow:hidden">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Revenue & Orders <small class="text-muted fw-normal ms-2">Last 7 days</small></span>
                <span class="badge bg-light text-dark border">Chart.js</span>
            </div>
            <div class="card-body">
                <canvas id="revChart" height="140"></canvas>
                <div class="d-flex gap-3 mt-3 small">
                    <span><span class="d-inline-block rounded-circle me-1" style="width:9px;height:9px;background:var(--primary)"></span> Revenue</span>
                    <span><span class="d-inline-block rounded-circle me-1" style="width:9px;height:9px;background:#f59e0b"></span> Orders</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100" style="border-radius:16px; overflow:hidden">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Restaurants</span>
                <a href="{{ route('admin.restaurants') }}" class="btn btn-sm btn-outline-secondary">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentRestaurants??[] as $r)
                    <div class="list-group-item d-flex gap-3 align-items-center" style="border-color:#f1f5f3">
                        <span class="d-inline-grid place-items-center rounded-3 flex-shrink-0" style="width:38px;height:38px; display:grid; background:var(--primary-50); color:var(--primary); font-weight:800">{{ strtoupper(substr($r->name,0,1)) }}</span>
                        <span class="flex-fill" style="min-width:0">
                            <span class="d-block fw-semibold small" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $r->name }}</span>
                            <span class="small text-muted">{{ $r->city }}, {{ $r->state }} · {{ $r->created_at->diffForHumans(null,true) }} ago</span>
                        </span>
                        @if($r->status==='pending')<span class="badge" style="background:#fef9c3; color:#854d0e">Pending</span>
                        @elseif($r->status==='active')<span class="badge" style="background:#dcfce7; color:#166534">Active</span>
                        @else<span class="badge bg-light text-dark border text-capitalize">{{ $r->status }}</span>@endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-4 small">No restaurants yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3" style="border-radius:16px; overflow:hidden">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Recent Orders</span>
        <a href="{{ route('admin.orders') }}" class="small text-decoration-none">View all →</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background:#f8faf9; border-bottom:1px solid #e8efec"><tr class="small text-muted" style="letter-spacing:.04em; text-transform:uppercase"><th class="ps-4 py-3">Order</th><th class="py-3">Restaurant</th><th class="py-3">Customer</th><th class="py-3">Total</th><th class="py-3">Status</th><th class="pe-4 py-3 text-end">Time</th></tr></thead>
                <tbody>
                @forelse($recentOrders??[] as $o)
                    <tr>
                        <td class="ps-4 fw-semibold small">{{ $o->order_number }}</td>
                        <td class="small">{{ $o->restaurant->name ?? '—' }}</td>
                        <td class="small">{{ $o->customer_name }}<br><span class="text-muted" style="font-size:.78rem">{{ $o->customer_mobile }}</span></td>
                        <td class="fw-semibold small">{{ \App\Support\Money::format($o->total) }}</td>
                        <td><span class="badge badge-soft" style="background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe">{{ $o->status }}</span></td>
                        <td class="pe-4 text-end small text-muted">{{ $o->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted small">No orders yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revChart');
if(ctx){
    new Chart(ctx, {
        type:'line',
        data:{
            labels:['Mon','Tue','Wed','Thu','Fri','Sat','Today'],
            datasets:[
                {label:'Revenue', data:[12000,19000,15000,22000,18000,25000,{{ $stats['today_revenue']??0 }}], borderColor:'#0f6b57', backgroundColor:'rgba(15,107,87,.08)', fill:true, tension:.35, pointRadius:0},
                {label:'Orders', data:[8,12,9,14,11,16,{{ $stats['today_orders']??0 }}], borderColor:'#f59e0b', backgroundColor:'transparent', tension:.35, pointRadius:0, yAxisID:'y1'}
            ]
        },
        options:{
            responsive:true, interaction:{mode:'index', intersect:false},
            scales:{ y:{ beginAtZero:true, grid:{color:'#f1f5f3'} }, y1:{ position:'right', grid:{display:false}, beginAtZero:true } },
            plugins:{ legend:{display:false} }
        }
    });
}
</script>
@endpush
@endsection