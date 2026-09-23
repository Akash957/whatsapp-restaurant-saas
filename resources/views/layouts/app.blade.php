<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'WhatsApp Restaurant SaaS'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #16876b; --secondary: #f1f8f5; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: #126e58; border-color: #126e58; }
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background: var(--primary) !important; }
        .hero { background: linear-gradient(135deg, var(--primary) 0%, #0f5a48 100%); }
    </style>
    @stack('styles')
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/"><i class="bi bi-shop"></i> {{ config('app.name', 'RestroSaaS') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                @auth
                    @if(auth()->user()->hasRole('super_admin'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @elseif(auth()->user()->hasRole('vendor'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->hasRole('staff'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->hasRole('customer'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.dashboard') }}">My Orders</a></li>
                    @endif
                    <li class="nav-item"><form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button class="btn btn-outline-light btn-sm">Logout ({{ auth()->user()->name }})</button></form></li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm text-primary fw-semibold" href="{{ route('register') }}">Get Started</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@if(session('status'))<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button class="btn-close" data-bs-dismiss="alert"></button></div></div>@endif
@if(session('success'))<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div></div>@endif
@if(session('error'))<div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div></div>@endif

<main>@yield('content')</main>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">&copy; {{ date('Y') }} {{ config('app.name', 'WhatsApp Restaurant SaaS') }}. All rights reserved.</p>
        <small class="text-white-50">Build your online restaurant ordering system with WhatsApp.</small>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>