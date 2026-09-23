@extends('layouts.app')
@section('title', 'WhatsApp Restaurant Ordering SaaS - Build your online restaurant')
@section('content')
{{-- Hero --}}
<section class="bg-white border-bottom">
    <div class="container py-5 py-lg-6">
        <div class="row align-items-center g-5 py-4">
            <div class="col-lg-6">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 mb-3"><i class="bi bi-whatsapp me-1"></i> WhatsApp Ordering Ready</span>
                <h1 class="display-5 fw-bold lh-sm">WhatsApp Restaurant Ordering SaaS</h1>
                <p class="lead text-muted mt-3">Build your online restaurant in minutes. Accept orders on WhatsApp, manage menus, track deliveries and grow sales — all from one dashboard.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4">Get Started <i class="bi bi-arrow-right ms-1"></i></a>
                    <a href="{{ route('menu', ['restaurant' => 'demo']) }}" onclick="event.preventDefault(); document.getElementById('features').scrollIntoView({behavior:'smooth'})" class="btn btn-outline-secondary btn-lg px-4">View Demo</a>
                </div>
                <div class="d-flex gap-4 mt-4 small text-muted">
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> 14-day trial</span>
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> No credit card</span>
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Cancel anytime</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light border rounded-4 p-3 p-lg-4 shadow-sm">
                    <div class="bg-white rounded-3 border p-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;"><i class="bi bi-shop text-white fs-5"></i></div>
                            <div><div class="fw-semibold">Demo Restaurant</div><small class="text-muted">Online • 30-40 mins delivery</small></div>
                            <span class="ms-auto badge bg-success">Open</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><div class="border rounded p-2 text-center"><div class="fw-bold">🍕</div><small>Margherita</small><div class="small text-success fw-semibold">₹199</div></div></div>
                            <div class="col-6"><div class="border rounded p-2 text-center"><div class="fw-bold">🍔</div><small>Veg Burger</small><div class="small text-success fw-semibold">₹149</div></div></div>
                            <div class="col-6"><div class="border rounded p-2 text-center"><div class="fw-bold">🍝</div><small>Pasta</small><div class="small text-success fw-semibold">₹249</div></div></div>
                            <div class="col-6"><div class="border rounded p-2 text-center"><div class="fw-bold">🥤</div><small>Cold Coffee</small><div class="small text-success fw-semibold">₹99</div></div></div>
                        </div>
                        <button class="btn btn-success w-100 mt-3"><i class="bi bi-whatsapp me-2"></i>Order on WhatsApp</button>
                    </div>
                    <div class="text-center small text-muted mt-3">Trusted by 2,500+ restaurants across India</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mx-auto mb-5" style="max-width:640px;">
            <h2 class="fw-bold">Everything you need to sell online</h2>
            <p class="text-muted">From menu to WhatsApp, payments to analytics — built for busy restaurant owners.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="bg-primary bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3"><i class="bi bi-phone fs-4 text-primary"></i></div><h5>WhatsApp Ordering</h5><p class="text-muted small mb-0">Customers order directly on WhatsApp. Automated messages, order updates and abandoned cart reminders.</p></div></div></div>
            <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="bg-success bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3"><i class="bi bi-menu-button-wide fs-4 text-success"></i></div><h5>Menu Management</h5><p class="text-muted small mb-0">Categories, variants, add-ons, pricing, stock and scheduling with drag-and-drop simplicity.</p></div></div></div>
            <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="bg-warning bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3"><i class="bi bi-credit-card fs-4 text-warning"></i></div><h5>Payments & COD</h5><p class="text-muted small mb-0">Razorpay, COD, delivery charges, taxes, coupons and tips all calculated accurately.</p></div></div></div>
            <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="bg-info bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3"><i class="bi bi-truck fs-4 text-info"></i></div><h5>Delivery & Tracking</h5><p class="text-muted small mb-0">Delivery, pickup, dine-in modes with live tracking and WhatsApp status updates.</p></div></div></div>
            <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="bg-danger bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3"><i class="bi bi-graph-up fs-4 text-danger"></i></div><h5>Analytics & Reports</h5><p class="text-muted small mb-0">Sales, orders, top products and revenue charts to grow your business faster.</p></div></div></div>
            <div class="col-md-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="bg-secondary bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3"><i class="bi bi-palette fs-4 text-secondary"></i></div><h5>Branding & Domain</h5><p class="text-muted small mb-0">Your logo, colors, cover image and custom domain with white-label options.</p></div></div></div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-4">
        <div class="text-center mb-5"><h2 class="fw-bold">How It Works</h2><p class="text-muted">Launch in 4 simple steps</p></div>
        <div class="row g-4 text-center">
            <div class="col-6 col-lg-3"><div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><span class="fs-4 fw-bold text-primary">1</span></div><h6>Register Restaurant</h6><p class="small text-muted">Create account with your restaurant details.</p></div>
            <div class="col-6 col-lg-3"><div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><span class="fs-4 fw-bold text-primary">2</span></div><h6>Add Menu</h6><p class="small text-muted">Add categories, products, variants & add-ons.</p></div>
            <div class="col-6 col-lg-3"><div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><span class="fs-4 fw-bold text-primary">3</span></div><h6>Share Link</h6><p class="small text-muted">Share your storefront or QR with customers.</p></div>
            <div class="col-6 col-lg-3"><div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><span class="fs-4 fw-bold text-primary">4</span></div><h6>Receive Orders</h6><p class="small text-muted">Get WhatsApp orders & manage from panel.</p></div>
        </div>
    </div>
</section>

{{-- WhatsApp section --}}
<section class="py-5" style="background:#f0fdf4;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="fw-bold">Sell where your customers already are — <span class="text-success">WhatsApp</span></h2>
                <p class="text-muted">No app downloads. Customers browse your menu, add to cart and checkout — then get confirmations on WhatsApp. Automate greetings, order updates and feedback requests.</p>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Automated order confirmations</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Abandoned cart reminders</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Delivery status templates</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> AI bot for FAQs & menu</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-success mt-2"><i class="bi bi-whatsapp me-2"></i>Enable WhatsApp Ordering</a>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3 mb-3">
                            <div class="bg-success rounded-circle" style="width:40px;height:40px;"></div>
                            <div class="bg-light rounded-3 p-3 small flex-grow-1">Hi 👋 Welcome to Spice Villa! Tap below to view our menu.</div>
                        </div>
                        <div class="bg-success text-white rounded-3 p-3 small ms-5 mb-3">View Menu <i class="bi bi-box-arrow-up-right ms-2"></i></div>
                        <div class="bg-light rounded-3 p-3 small">Your order #SPV-1024 is confirmed! 🍕 Preparing — we'll notify you when it's out for delivery.</div>
                        <div class="text-center small text-muted mt-3">End-to-end encrypted</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Pricing teaser --}}
<section class="py-5 bg-white">
    <div class="container py-4 text-center">
        <h2 class="fw-bold">Simple, transparent pricing</h2>
        <p class="text-muted mb-4">Choose the plan that fits your restaurant. Upgrade anytime.</p>
        <a href="{{ url('/pricing') }}" class="btn btn-primary btn-lg px-5">View Pricing Plans</a>
        <p class="small text-muted mt-2">Starting from ₹499/month • Save 20% yearly</p>
    </div>
</section>

{{-- FAQ --}}
<section class="py-5 bg-light border-top">
    <div class="container py-4">
        <h2 class="fw-bold text-center mb-5">Frequently Asked Questions</h2>
        <div class="row justify-content-center"><div class="col-lg-8">
            <div class="accordion" id="faq">
                <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#f1">Do customers need to install an app?</button></h2><div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faq"><div class="accordion-body small text-muted">No. Customers order from your web storefront and receive updates on WhatsApp.</div></div></div>
                <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#f2">Can I use my own domain?</button></h2><div id="f2" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small text-muted">Yes, Professional and Enterprise plans support custom domains with white-label.</div></div></div>
                <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#f3">How do payments work?</button></h2><div id="f3" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small text-muted">Razorpay for online payments and COD. Delivery, tax and coupons are automated.</div></div></div>
                <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#f4">Is there a free trial?</button></h2><div id="f4" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small text-muted">Yes, 14-day free trial on all plans with no credit card required.</div></div></div>
            </div>
        </div></div>
    </div>
</section>

{{-- CTA --}}
<section class="py-5 bg-dark text-white">
    <div class="container text-center py-4">
        <h2 class="fw-bold">Ready to grow your restaurant online?</h2>
        <p class="text-white-50 mb-4">Join hundreds of restaurants already selling on WhatsApp.</p>
        <a href="{{ route('register') }}" class="btn btn-success btn-lg px-5">Get Started Free</a>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 ms-2">Login</a>
    </div>
</section>
@endsection
