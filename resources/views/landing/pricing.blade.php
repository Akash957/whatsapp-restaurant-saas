@extends('layouts.app')
@section('title','Pricing - '.config('app.name'))
@section('content')
<section class="bg-white border-bottom py-5">
    <div class="container text-center py-4">
        <h1 class="fw-bold">Pricing Plans</h1>
        <p class="text-muted mx-auto" style="max-width:600px;">Choose the perfect plan for your restaurant. All plans include a 14-day free trial.</p>
    </div>
</section>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @forelse($plans as $plan)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm {{ $loop->index===1?'border border-success border-2':'' }} position-relative">
                        @if($loop->index===1)<span class="badge bg-success position-absolute top-0 start-50 translate-middle px-3 py-2 rounded-pill">Most Popular</span>@endif
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                            <p class="small text-muted">{{ $plan->description }}</p>
                            <div class="my-3">
                                <span class="display-6 fw-bold">{{ \App\Support\Money::format($plan->price) }}</span>
                                <span class="text-muted">/ {{ $plan->duration_days }} days</span>
                            </div>
                            <ul class="list-unstyled small mb-4 flex-grow-1">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $plan->max_products }} Products</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $plan->max_orders }} Orders / month</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $plan->max_staff }} Staff accounts</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $plan->whatsapp_messages }} WhatsApp messages</li>
                                <li class="mb-2"><i class="bi {{ $plan->ai_automation?'bi-check-circle-fill text-success':'bi-x-circle text-muted' }} me-2"></i> AI Automation {{ $plan->ai_automation?'':' (not included)' }}</li>
                                <li class="mb-2"><i class="bi {{ $plan->custom_domain?'bi-check-circle-fill text-success':'bi-x-circle text-muted' }} me-2"></i> Custom Domain {{ $plan->custom_domain?'':' (not included)' }}</li>
                                <li class="mb-2"><i class="bi {{ $plan->white_label?'bi-check-circle-fill text-success':'bi-x-circle text-muted' }} me-2"></i> White Label {{ $plan->white_label?'':' (not included)' }}</li>
                                <li class="mb-2"><i class="bi {{ $plan->reports?'bi-check-circle-fill text-success':'bi-x-circle text-muted' }} me-2"></i> Reports</li>
                                @if($plan->support)<li class="mb-2"><i class="bi bi-headset me-2 text-primary"></i> Support: {{ $plan->support }}</li>@endif
                            </ul>
                            <a href="{{ route('register') }}" class="btn {{ $loop->index===1?'btn-success':'btn-outline-primary' }} w-100">Get Started</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No pricing plans available at the moment.</p>
                    <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-5">
            <p class="small text-muted">Need a custom plan? <a href="mailto:support@example.com">Contact sales</a></p>
        </div>
    </div>
</section>
@endsection
