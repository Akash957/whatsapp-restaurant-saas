@extends('layouts.app')
@section('title', 'Menu - '.$restaurant->name)
@section('content')
<div class="container py-4">
<h3 class="fw-bold mb-3">{{ $restaurant->name }} — Menu</h3>
<form method="GET" class="row g-2 mb-3"><div class="col-md-5"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search..."></div><div class="col-md-4"><select name="category" class="form-select"><option value="">All Categories</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>@endforeach</select></div><div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div></form>
<div class="row g-3">
@forelse($products as $p)<div class="col-6 col-md-3"><div class="card h-100 shadow-sm"><div class="bg-light d-flex align-items-center justify-content-center" style="height:130px"><i class="bi bi-image fs-2 text-muted"></i></div><div class="card-body p-3"><div class="small text-muted">{{ $p->category->name }}</div><h6><a href="{{ route('product',[$restaurant->slug,$p->slug]) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></h6><div class="fw-bold text-primary">{{ \App\Support\Money::format($p->discount_price ?? $p->price) }}</div><form method="POST" action="{{ route('cart.add',$restaurant->slug) }}" class="mt-2">@csrf<input type="hidden" name="product_id" value="{{ $p->id }}"><input type="hidden" name="quantity" value="1"><button class="btn btn-sm btn-primary w-100">Add to Cart</button></form></div></div></div>@empty<div class="col-12 text-center text-muted py-5">No products found.</div>@endforelse
</div>
<div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection