<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel - '.config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary:#0f6b57; --primary-600:#0d5a4a; --primary-50:#e6f4f0; --primary-100:#cce9e0;
            --sidebar:280px; --sidebar-bg:#0a2e26; --sidebar-hover:#123d32; --surface:#f6f8f7;
        }
        *{font-family:'Inter',system-ui,-apple-system,sans-serif}
        .brand-font{font-family:'Plus Jakarta Sans',Inter,sans-serif}
        .btn-primary{background:var(--primary);border-color:var(--primary);font-weight:600}
        .btn-primary:hover{background:var(--primary-600);border-color:var(--primary-600)}
        .text-primary{color:var(--primary)!important}
        .bg-primary{background:var(--primary)!important}
        .sidebar{
            width:var(--sidebar); min-height:100vh; background:var(--sidebar-bg);
            position:fixed; inset:0 auto 0 0; display:flex; flex-direction:column; z-index:1040;
            border-right:1px solid rgba(255,255,255,.06);
        }
        .sidebar .brand{
            padding:20px 18px 18px; border-bottom:1px solid rgba(255,255,255,.07);
            display:flex; align-items:center; gap:10px; color:#fff; font-weight:700; font-size:1.08rem;
        }
        .sidebar .brand .logo{
            width:36px; height:36px; border-radius:10px; display:grid; place-items:center;
            background:var(--primary); color:#fff; font-size:18px; flex-shrink:0;
            box-shadow:0 4px 14px rgba(15,107,87,.35);
        }
        .sidebar nav{flex:1; overflow:auto; padding:14px 10px}
        .sidebar nav::-webkit-scrollbar{width:0}
        .sidebar .section{
            font-size:.68rem; letter-spacing:.09em; text-transform:uppercase; color:rgba(255,255,255,.42);
            font-weight:700; padding:14px 10px 6px;
        }
        .sidebar a.item{
            color:#c7ddd6; text-decoration:none; display:flex; align-items:center; gap:10px;
            padding:9px 10px; border-radius:10px; font-size:.90rem; font-weight:500; margin:1px 0;
            transition:.15s; position:relative;
        }
        .sidebar a.item i{font-size:17px; width:22px; text-align:center; opacity:.95}
        .sidebar a.item:hover{background:var(--sidebar-hover); color:#fff}
        .sidebar a.item.active{background:var(--primary); color:#fff; box-shadow:0 4px 12px rgba(0,0,0,.18)}
        .sidebar a.item .count{margin-left:auto; background:rgba(255,255,255,.14); color:#fff; font-size:.70rem; font-weight:700; padding:2px 7px; border-radius:999px}
        .sidebar .userbox{padding:14px; border-top:1px solid rgba(255,255,255,.07); background:rgba(255,255,255,.02)}
        .main{margin-left:var(--sidebar); min-height:100vh; background:var(--surface); display:flex; flex-direction:column; min-width:0}
        .topbar{
            height:62px; background:#fff; border-bottom:1px solid #e8efec; display:flex; align-items:center; gap:14px;
            padding:0 24px; position:sticky; top:0; z-index:20;
        }
        .topbar .page-title{font-weight:700; font-size:1.05rem; letter-spacing:-.01em}
        .content{flex:1; padding:22px 24px 28px}
        .stat-card{border:0; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.03); overflow:hidden}
        .card{border:0; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.03)}
        .card-header{background:#fff; border-bottom:1px solid #eef3f1; font-weight:700}
        .badge-soft{font-weight:600; letter-spacing:.01em; padding:5px 9px; border-radius:999px}
        @media(max-width:991px){
            .sidebar{transform:translateX(-100%); transition:transform .22s ease}
            .sidebar.show{transform:translateX(0)}
            .main{margin-left:0}
            .content{padding:16px}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <span class="logo"><i class="bi bi-shop"></i></span>
            <span>
                <span class="brand-font" style="display:block; line-height:1">RestroSaaS</span>
                <small style="font-weight:500; color:rgba(255,255,255,.58); font-size:.72rem; letter-spacing:.02em">{{ auth()->user()->role==='super_admin' ? 'Super Admin' : (auth()->user()->restaurant->name ?? 'Panel') }}</small>
            </span>
            <button class="btn btn-sm ms-auto d-lg-none text-white" onclick="document.getElementById('sidebar').classList.remove('show')" style="border:1px solid rgba(255,255,255,.12)"><i class="bi bi-x-lg"></i></button>
        </div>
        <nav>
            @php $r = auth()->user()->role ?? 'guest'; $isAdmin = $r==='super_admin'; $isVendor=$r==='vendor'; $route=request()->route()->getName() ?? ''; @endphp
            @if($isAdmin)
                <div class="section">Overview</div>
                <a class="item {{ str_starts_with($route,'admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
                <a class="item {{ str_starts_with($route,'admin.restaurants')?'active':'' }}" href="{{ route('admin.restaurants') }}"><i class="bi bi-shop"></i> Restaurants @php $pc=\App\Models\Restaurant::where('status','pending')->count(); @endphp @if($pc)<span class="count">{{ $pc }}</span>@endif</a>
                <div class="section">Operations</div>
                <a class="item {{ str_starts_with($route,'admin.orders')?'active':'' }}" href="{{ route('admin.orders') }}"><i class="bi bi-receipt-cutoff"></i> Orders</a>
                <a class="item {{ str_starts_with($route,'admin.products')?'active':'' }}" href="{{ route('admin.products') }}"><i class="bi bi-box-seam"></i> Products</a>
                <a class="item {{ str_starts_with($route,'admin.categories')?'active':'' }}" href="{{ route('admin.categories') }}"><i class="bi bi-tags"></i> Categories</a>
                <a class="item {{ str_starts_with($route,'admin.customers')?'active':'' }}" href="{{ route('admin.customers') }}"><i class="bi bi-people"></i> Customers</a>
                <a class="item {{ str_starts_with($route,'admin.staff')?'active':'' }}" href="{{ route('admin.staff') }}"><i class="bi bi-person-badge"></i> Staff</a>
                <div class="section">Business</div>
                <a class="item {{ str_starts_with($route,'admin.subscriptions')?'active':'' }}" href="{{ route('admin.subscriptions') }}"><i class="bi bi-credit-card-2-front"></i> Subscriptions</a>
                <a class="item {{ str_starts_with($route,'admin.payments')?'active':'' }}" href="{{ route('admin.payments') }}"><i class="bi bi-wallet2"></i> Payments</a>
                <a class="item {{ str_starts_with($route,'admin.whatsapp')?'active':'' }}" href="{{ route('admin.whatsapp') }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                <a class="item {{ str_starts_with($route,'admin.reports')?'active':'' }}" href="{{ route('admin.reports') }}"><i class="bi bi-bar-chart-line"></i> Reports</a>
                <a class="item {{ str_starts_with($route,'admin.activity')?'active':'' }}" href="{{ route('admin.activity-logs') }}"><i class="bi bi-clock-history"></i> Activity Logs</a>
                <div class="section">System</div>
                <a class="item {{ str_starts_with($route,'admin.system')||str_starts_with($route,'admin.website')?'active':'' }}" href="{{ route('admin.system-settings') }}"><i class="bi bi-gear"></i> Settings</a>
            @elseif($isVendor)
                <div class="section">Overview</div>
                <a class="item {{ $route==='vendor.dashboard'?'active':'' }}" href="{{ route('vendor.dashboard') }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
                <div class="section">Menu</div>
                <a class="item {{ str_contains($route,'categories')?'active':'' }}" href="{{ route('vendor.categories') }}"><i class="bi bi-tags"></i> Categories</a>
                <a class="item {{ str_contains($route,'products')||str_contains($route,'variants')?'active':'' }}" href="{{ route('vendor.products') }}"><i class="bi bi-box-seam"></i> Products</a>
                <a class="item {{ str_contains($route,'addons')?'active':'' }}" href="{{ route('vendor.addons') }}"><i class="bi bi-plus-square-dotted"></i> Variants &amp; Add-ons</a>
                <div class="section">Orders</div>
                <a class="item {{ str_contains($route,'orders')?'active':'' }}" href="{{ route('vendor.orders') }}"><i class="bi bi-receipt-cutoff"></i> Orders</a>
                <a class="item {{ str_contains($route,'coupons')?'active':'' }}" href="{{ route('vendor.coupons') }}"><i class="bi bi-ticket-perforated"></i> Coupons</a>
                <a class="item {{ str_contains($route,'customers')?'active':'' }}" href="{{ route('vendor.dashboard') }}"><i class="bi bi-people"></i> Customers</a>
                <div class="section">Growth</div>
                <a class="item {{ str_contains($route,'whatsapp')?'active':'' }}" href="{{ route('vendor.whatsapp') }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                <a class="item {{ str_contains($route,'qr')?'active':'' }}" href="{{ route('vendor.qr.code') }}"><i class="bi bi-qr-code"></i> QR Code</a>
                <a class="item {{ str_contains($route,'subscription')?'active':'' }}" href="{{ route('vendor.subscription') }}"><i class="bi bi-gem"></i> Subscription</a>
                <div class="section">Settings</div>
                <a class="item {{ $route==='vendor.profile'?'active':'' }}" href="{{ route('vendor.profile') }}"><i class="bi bi-shop-window"></i> Restaurant Profile</a>
                <a class="item {{ str_contains($route,'business-hours')?'active':'' }}" href="{{ route('vendor.business-hours') }}"><i class="bi bi-clock"></i> Business Hours</a>
                <a class="item {{ $route==='vendor.settings'?'active':'' }}" href="{{ route('vendor.settings') }}"><i class="bi bi-sliders"></i> Delivery &amp; Branding</a>
            @elseif($r==='staff')
                <a class="item {{ $route==='staff.dashboard'?'active':'' }}" href="{{ route('staff.dashboard') }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
                <a class="item {{ str_contains($route,'orders')?'active':'' }}" href="{{ route('staff.orders') }}"><i class="bi bi-receipt-cutoff"></i> Orders</a>
                <a class="item" href="{{ route('staff.customers') }}"><i class="bi bi-people"></i> Customers</a>
            @endif
        </nav>
        <div class="userbox">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="d-inline-grid place-items-center rounded-circle bg-white text-dark fw-bold" style="width:34px;height:34px;display:grid">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                <span class="flex-fill" style="min-width:0">
                    <span class="d-block text-white fw-semibold small" style="line-height:1.1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ auth()->user()->name }}</span>
                    <small style="color:rgba(255,255,255,.58); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block">{{ auth()->user()->email }}</small>
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm w-100 fw-semibold" style="background:rgba(255,255,255,.09); color:#fff; border:1px solid rgba(255,255,255,.14)"><i class="bi bi-box-arrow-right me-1"></i> Logout</button></form>
        </div>
    </aside>
    <div class="main">
        <div class="topbar">
            <button class="btn btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.add('show')"><i class="bi bi-list"></i></button>
            <div class="page-title brand-font">@yield('page_title','Dashboard')</div>
            <div class="ms-auto d-flex align-items-center gap-2">
                @if(auth()->user()->restaurant)
                    @php $rs=auth()->user()->restaurant; @endphp
                    <span class="d-none d-md-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill border bg-white small">
                        <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background: {{ $rs->status==='active'?'#16a34a':($rs->status==='pending'?'#f59e0b':'#dc2626') }}"></span>
                        {{ $rs->name }} · <span class="{{ $rs->status==='active'?'text-success':($rs->status==='pending'?'text-warning':'text-danger') }} fw-semibold text-capitalize">{{ $rs->status }}</span>
                    </span>
                    <a href="{{ route('restaurant.home',$rs->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex"><i class="bi bi-box-arrow-up-right me-1"></i> View Store</a>
                @endif
                <span class="d-inline-grid place-items-center rounded-circle" style="width:36px;height:36px;background:var(--primary-50); color:var(--primary); font-weight:700">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
            </div>
        </div>
        <div class="content">
            @if(session('status'))<div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" style="border-radius:12px">{{ session('status') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if(session('success'))<div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" style="border-radius:12px">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if(session('error'))<div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" style="border-radius:12px">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if($errors->any())<div class="alert alert-danger border-0" style="border-radius:12px"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
        <div class="px-4 py-3 small text-muted border-top bg-white d-flex flex-wrap gap-2 justify-content-between">
            <span>© {{ date('Y') }} {{ config('app.name','RestroSaaS') }} · Crafted for restaurants that sell on WhatsApp</span>
            <span>Support: hello@restrosaas.test · Docs</span>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>