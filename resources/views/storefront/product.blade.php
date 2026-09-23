@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-4">
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('restaurant.home',$restaurant->slug) }}">{{ $restaurant->name }}</a></li><li class="breadcrumb-item"><a href="{{ route('menu',$restaurant->slug) }}">Menu</a></li><li class="breadcrumb-item active">{{ $product->name }}</li></ol></nav>
<div class="row g-4">
<div class="col-md-6"><div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:320px"><i class="bi bi-image fs-1 text-muted"></i></div></div>
<div class="col-md-6">
<h2 class="fw-bold">{{ $product->name }}</h2><p class="text-muted">{{ $product->category->name }}</p><p>{{ $product->description }}</p>
<div class="h4 text-primary">{{ \App\Support\Money::format($product->discount_price ?? $product->price) }} @if($product->discount_price)<small class="text-muted text-decoration-line-through fs-6">{{ \App\Support\Money::format($product->price) }}</small>@endif</div>
<form method="POST" action="{{ route('cart.add',$restaurant->slug) }}">@csrf
<input type="hidden" name="product_id" value="{{ $product->id }}">
@if($product->variants->count())<div class="mb-3"><label class="form-label fw-semibold">Variant</label>@foreach($product->variants as $v)<label class="form-check"><input type="radio" name="variant_id" value="{{ $v->id }}" class="form-check-input" @checked($loop->first)> {{ $v->name }} — {{ \App\Support\Money::format($v->price) }}</label>@endforeach</div>@endif
@foreach($product->addons as $addon)<div class="mb-3"><label class="form-label fw-semibold">{{ $addon->name }} @if($addon->is_required)*@endif <small class="text-muted">(max {{ $addon->max_selections }})</small></label>@foreach($addon->items as $it)<label class="form-check"><input type="checkbox" name="addon_ids[]" value="{{ $it->id }}" class="form-check-input"> {{ $it->name }} +{{ \App\Support\Money::format($it->price) }}</label>@endforeach</div>@endforeach
<div class="mb-3" style="max-width:160px"><label class="form-label">Quantity</label><input type="number" name="quantity" value="1" min="1" max="50" class="form-control" required></div>
<button class="btn btn-primary btn-lg w-100"><i class="bi bi-cart-plus"></i> Add to Cart</button>
</form>
</div>
</div>
</div>
@endsection