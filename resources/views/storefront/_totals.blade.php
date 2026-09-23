@props(['quote'])
@if($quote)
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Order Summary</h6>
        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Subtotal</span><span>{{ \App\Support\Money::format($quote['subtotal']) }}</span></div>
        @if($quote['discount']>0)
            <div class="d-flex justify-content-between small mb-2 text-success"><span>Discount</span><span>-{{ \App\Support\Money::format($quote['discount']) }}</span></div>
        @endif
        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Tax</span><span>{{ \App\Support\Money::format($quote['tax']) }}</span></div>
        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Delivery</span><span>{{ \App\Support\Money::format($quote['delivery_charge']) }}</span></div>
        @if($quote['tips']>0)
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Tips</span><span>{{ \App\Support\Money::format($quote['tips']) }}</span></div>
        @endif
        <hr>
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>{{ \App\Support\Money::format($quote['total']) }}</span></div>
        <div class="small text-muted mt-1">{{ count($quote['items']) }} item(s)</div>
    </div>
</div>
@endif
