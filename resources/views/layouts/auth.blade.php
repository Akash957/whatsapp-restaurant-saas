<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth - '.config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>:root{--primary:#16876b} .btn-primary{background:var(--primary);border-color:var(--primary)}</style>
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-4">
<div class="container" style="max-width:520px">
    <div class="text-center mb-4">
        <a href="/" class="text-decoration-none h4 fw-bold text-dark"><i class="bi bi-shop text-primary"></i> {{ config('app.name','RestroSaaS') }}</a>
    </div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <div class="card shadow-sm border-0"><div class="card-body p-4 p-md-5">@yield('content')</div></div>
    <p class="text-center text-muted small mt-3">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>