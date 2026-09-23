@props(['products','restaurant'])
<div class="row g-3 g-md-4">
    @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <a href="{{ route('product',['restaurant'=>$restaurant->slug,'product'=>$product->slug]) }}" class="text-decoration-none">
                    <div class="ratio ratio-4x3 bg-light">
                        @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="object-fit-cover w-100 h-100">
                        @else
                            <div class="d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>
                        @endif
                    </div>
                </a>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="small text-muted">{{ $product->category->name ?? '' }}</div>
                    <h6 class="mb-1"><a href="{{ route('product',['restaurant'=>$restaurant->slug,'product'=>$product->slug]) }}" class="text-dark text-decoration-none">{{ $product->name }}</a></h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($product->discount_price)
                            <span class="fw-bold text-success">{{ \App\Support\Money::format($product->discount_price) }}</span>
                            <span class="small text-muted text-decoration-line-through">{{ \App\Support\Money::format($product->price) }}</span>
                        @else
                            <span class="fw-bold text-dark">{{ \App\Support\Money::format($product->price) }}</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('cart.add',$restaurant->slug) }}" class="mt-auto">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">No products found.</div>
    @endforelse
</div>
