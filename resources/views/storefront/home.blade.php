@extends('layouts.app')
@section('title', $restaurant->name)
@section('content')
<div class="py-4" style="background: {{ $restaurant->primary_color }}10">
<div class="container">
<div class="row align-items-center g-4">
<div class="col-md-7"><h1 class="fw-bold" style="color: {{ $restaurant->primary_color }}">{{ $restaurant->name }}</h1><p class="text-muted">{{ $restaurant->description ?? 'Delicious food delivered fast' }}</p><p class="small"><i class="bi bi-geo-alt"></i> {{ $restaurant->address }}, {{ $restaurant->city }} · <i class="bi bi-telephone"></i> {{ $restaurant->phone }}</p><a href="{{ route('menu',$restaurant->slug) }}" class="btn btn-primary">Browse Menu</a> <a href="{{ route('contact',$restaurant->slug) }}" class="btn btn-outline-secondary">Contact</a></div>
<div class="col-md-5 text-center"><div class="bg-white rounded shadow-sm d-flex align-items-center justify-content-center" style="height:200px"><i class="bi bi-image fs-1 text-muted"></i></div></div>
</div>
</div>
</div>
<div class="container py-4">
<form method="GET" action="{{ route('menu',$restaurant->slug) }}" class="mb-4"><div class="input-group"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search food..."><button class="btn btn-primary"><i class="bi bi-search"></i> Search</button></div></form>
<div class="d-flex gap-2 flex-wrap mb-4">@foreach($categories as $cat)<a href="{{ route('menu',$restaurant->slug) }}?category={{ $cat->id }}" class="btn btn-sm {{ request('category')==$cat->id?'btn-primary':'btn-outline-secondary' }}">{{ $cat->name }}</a>@endforeach</div>
<h5 class="fw-semibold mb-3">Featured</h5>
<div class="row g-3">
@forelse($products as $p)<div class="col-6 col-md-3"><div class="card h-100 shadow-sm"><div class="bg-light d-flex align-items-center justify-content-center" style="height:130px"><i class="bi bi-image fs-2 text-muted"></i></div><div class="card-body p-3"><div class="small text-muted">{{ $p->category->name ?? '' }}</div><h6 class="fw-semibold mb-1"><a href="{{ route('product',[$restaurant->slug,$p->slug]) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></h6><div class="d-flex align-items-center gap-2"><span class="fw-bold text-primary">{{ \App\Support\Money::format($p->discount_price ?? $p->price) }}</span>@if($p->discount_price)<small class="text-muted text-decoration-line-through">{{ \App\Support\Money::format($p->price) }}</small>@endif</div><form method="POST" action="{{ route('cart.add',$restaurant->slug) }}" class="mt-2">@csrf<input type="hidden" name="product_id" value="{{ $p->id }}"><input type="hidden" name="quantity" value="1"><button class="btn btn-sm btn-primary w-100"><i class="bi bi-cart-plus"></i> Add</button></form></div></div></div>@empty<p class="text-muted">No products yet.</p>@endforelse
</div>
<div class="mt-4 text-center"><a href="{{ route('menu',$restaurant->slug) }}" class="btn btn-outline-primary">View Full Menu</a></div>
</div>
@endsection