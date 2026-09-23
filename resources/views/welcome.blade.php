@extends('layouts.app')
@section('title','WhatsApp Restaurant Ordering SaaS')
@section('content')
<div class="hero text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold">WhatsApp Restaurant Ordering SaaS</h1>
                <p class="lead opacity-75">Build your online restaurant ordering system with WhatsApp. Let customers browse menus, order, and track — all via WhatsApp.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg text-primary fw-semibold">Get Started</a>
                    @if(($restaurants ?? collect())->count())<a href="#restaurants" class="btn btn-outline-light btn-lg">View Demo</a>@endif
                </div>
                <div class="d-flex gap-4 mt-4 small opacity-75">
                    <span><i class="bi bi-check-circle"></i> No commission</span>
                    <span><i class="bi bi-check-circle"></i> WhatsApp ordering</span>
                    <span><i class="bi bi-check-circle"></i> Own branding</span>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <div class="bg-white rounded-3 p-4 text-dark shadow">
                    <div class="fw-semibold mb-3"><i class="bi bi-whatsapp text-success"></i> WhatsApp Order Flow</div>
                    <div class="d-flex flex-column gap-2 small text-start mx-auto" style="max-width:320px">
                        <div class="bg-light rounded p-2">👋 Welcome! View Menu</div>
                        <div class="bg-success text-white rounded p-2 ms-4">🍕 Show me Pizzas</div>
                        <div class="bg-light rounded p-2">🍕 Margherita — ₹199 <button class="btn btn-sm btn-primary float-end">Add</button></div>
                        <div class="bg-success text-white rounded p-2 ms-4">Add to cart, Checkout</div>
                        <div class="bg-light rounded p-2">✅ Order #ORD-2026-000001 confirmed!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="text-center mb-5"><h2 class="fw-bold">Everything you need to run your restaurant online</h2><p class="text-muted">Powerful features, simple setup, your brand.</p></div>
    <div class="row g-4">
        @foreach([['bi-shop','Online Ordering','Beautiful menu, categories, variants, add-ons'],['bi-whatsapp','WhatsApp Ordering','Customers order directly via WhatsApp'],['bi-qr-code','QR Code','Table QR for dine-in ordering'],['bi-palette','White Label','Your logo, colors, domain'],['bi-graph-up','Reports','Sales, revenue, order analytics'],['bi-robot','AI Assistant','FAQ, recommendations, tracking']] as $f)
        <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body text-center p-4"><i class="bi {{ $f[0] }} fs-1 text-primary"></i><h6 class="fw-semibold mt-3">{{ $f[1] }}</h6><p class="small text-muted mb-0">{{ $f[2] }}</p></div></div></div>
        @endforeach
    </div>
</div>

<div class="bg-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6"><h3 class="fw-bold">How it works</h3><ol class="mt-3">
                <li class="mb-2"><strong>Register</strong> your restaurant — pending admin approval</li>
                <li class="mb-2"><strong>Setup</strong> menu, hours, delivery, WhatsApp</li>
                <li class="mb-2"><strong>Share</strong> link &amp; QR — customers browse &amp; order</li>
                <li class="mb-2"><strong>Receive</strong> orders + WhatsApp notifications</li>
            </ol></div>
            <div class="col-lg-6"><div class="card bg-light border-0 p-4"><h6 class="fw-semibold">Restaurant Benefits</h6><ul class="small mb-0"><li>Zero marketplace commission</li><li>Direct customer relationship via WhatsApp</li><li>Real-time order management</li><li>Subscription-based, predictable pricing</li></ul></div></div>
        </div>
    </div>
</div>

@if(isset($plans) && $plans->count())
<div class="container py-5" id="pricing">
    <h3 class="fw-bold text-center mb-4">Simple, transparent pricing</h3>
    <div class="row g-4 justify-content-center">
        @foreach($plans as $plan)
        <div class="col-md-4"><div class="card h-100 shadow-sm {{ $loop->index===1?'border-primary':'' }}">
            @if($loop->index===1)<div class="card-header bg-primary text-white text-center small fw-semibold">Most Popular</div>@endif
            <div class="card-body text-center p-4"><h5 class="fw-bold">{{ $plan->name }}</h5><div class="display-6 fw-bold">{{ \App\Support\Money::format($plan->price) }}<small class="fs-6 text-muted">/ {{ $plan->duration_days }} days</small></div><p class="small text-muted">{{ $plan->description }}</p><ul class="list-unstyled small text-start mt-3"><li><i class="bi bi-check text-success"></i> {{ $plan->max_products }} products</li><li><i class="bi bi-check text-success"></i> {{ $plan->max_orders }} orders/mo</li><li><i class="bi bi-check text-success"></i> {{ $plan->max_staff }} staff</li><li><i class="bi bi-check text-success"></i> {{ $plan->whatsapp_messages }} WA msgs</li></ul><a href="{{ route('register') }}" class="btn {{ $loop->index===1?'btn-primary':'btn-outline-primary' }} w-100 mt-3">Get Started</a></div>
        </div></div>
        @endforeach
    </div>
</div>
@endif

@if(isset($restaurants) && $restaurants->count())
<div class="bg-white py-5" id="restaurants">
    <div class="container"><h3 class="fw-bold text-center mb-4">Live Restaurants</h3><div class="row g-3">
        @foreach($restaurants as $r)<div class="col-md-3"><div class="card h-100 shadow-sm"><div class="card-body text-center"><div class="bg-light rounded mb-3 d-flex align-items-center justify-content-center" style="height:90px"><i class="bi bi-shop fs-1 text-muted"></i></div><h6 class="fw-semibold mb-1">{{ $r->name }}</h6><p class="small text-muted mb-2">{{ $r->city }}, {{ $r->state }}</p><a href="{{ route('restaurant.home',$r->slug) }}" class="btn btn-sm btn-outline-primary">Visit Menu</a></div></div></div>@endforeach
    </div></div>
</div>
@endif

<div class="container py-5">
    <h3 class="fw-bold text-center mb-4">Frequently Asked Questions</h3>
    <div class="accordion mx-auto" style="max-width:700px" id="faq">
        @foreach([['How does WhatsApp ordering work?','Customers chat with your WhatsApp number, browse menu, and place orders. You get notified instantly.'],['Do I need technical knowledge?','No. Just register, add menu items, configure business hours — you are live.'],['Can I use my own domain?','Yes, with Professional/Enterprise plans you can connect a custom domain.']] as $i=>$f)
        <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button {{ $i?'collapsed':'' }}" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">{{ $f[0] }}</button></h2><div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i==0?'show':'' }}" data-bs-parent="#faq"><div class="accordion-body small text-muted">{{ $f[1] }}</div></div></div>
        @endforeach
    </div>
</div>

<div class="bg-primary text-white py-5 text-center">
    <div class="container"><h3 class="fw-bold">Ready to grow your restaurant online?</h3><p class="opacity-75">Join hundreds of restaurants already selling with WhatsApp.</p><a href="{{ route('register') }}" class="btn btn-light btn-lg text-primary fw-semibold">Start for Free</a></div>
</div>
@endsection