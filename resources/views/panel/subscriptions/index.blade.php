@extends('layouts.panel')
@section('page_title','Subscription')
@section('content')
<div class="card mb-3"><div class="card-body">
@if($current)<div class="alert alert-success">Current Plan: <strong>{{ $current->plan->name }}</strong> — ends {{ $current->ends_at->format('d M Y') }} ({{ $current->status }})</div>@else<div class="alert alert-warning">No active subscription. Choose a plan below.</div>@endif
</div></div>
<div class="row g-3">
@foreach($plans as $plan)
<div class="col-md-4"><div class="card h-100 {{ $current && $current->subscription_plan_id==$plan->id ? 'border-success' : '' }}"><div class="card-body text-center"><h5>{{ $plan->name }}</h5><div class="h4">{{ \App\Support\Money::format($plan->price) }}<small class="text-muted fs-6">/ {{ $plan->duration_days }}d</small></div><p class="small text-muted">{{ $plan->description }}</p><ul class="list-unstyled small text-start"><li>• {{ $plan->max_products }} products</li><li>• {{ $plan->max_orders }} orders/mo</li><li>• {{ $plan->max_staff }} staff</li><li>• {{ $plan->whatsapp_messages }} WA msgs</li></ul>
<form method="POST" action="{{ route('vendor.subscription.update') }}">@csrf @method('PUT')<input type="hidden" name="plan_id" value="{{ $plan->id }}"><button class="btn btn-primary w-100" @if($current && $current->subscription_plan_id==$plan->id) disabled @endif>{{ $current && $current->subscription_plan_id==$plan->id ? 'Current' : 'Select' }}</button></form>
</div></div></div>
@endforeach
</div>
@endsection