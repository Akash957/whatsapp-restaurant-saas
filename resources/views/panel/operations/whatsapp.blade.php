@extends('layouts.panel')
@section('page_title','WhatsApp')
@section('content')
@php
    $isAdmin = auth()->user()->hasRole('super_admin');
    $route = $isAdmin ? route('admin.whatsapp.update') : route('vendor.whatsapp.update');
    $masked = fn($v) => $v ? substr($v,0,6).str_repeat('•', max(0, strlen($v)-10)).substr($v,-4) : '';
@endphp
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-end mb-3">
    <div>
        <h4 class="brand-font mb-1" style="font-weight:700; letter-spacing:-.02em">WhatsApp Business API</h4>
        <div class="text-muted small">Full-screen production setup — global config + per-restaurant override. Never expose tokens in frontend.</div>
    </div>
    <span class="badge {{ ($settings?->is_enabled ?? false) ? 'bg-success' : 'bg-secondary' }}" style="border-radius:999px; padding:7px 12px"><i class="bi bi-circle-fill me-1" style="font-size:.6rem"></i> {{ ($settings?->is_enabled ?? false) ? 'Enabled' : 'Disabled' }}</span>
</div>

<ul class="nav nav-pills mb-3 p-1" style="background:#fff; border-radius:12px; border:1px solid #e8efec; display:inline-flex">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#settings" style="border-radius:8px; font-weight:600">Settings</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#templates" style="border-radius:8px; font-weight:600">Templates</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#automation" style="border-radius:8px; font-weight:600">Automation</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#conversations" style="border-radius:8px; font-weight:600">Conversations</a></li>
</ul>

<div class="tab-content">
<div id="settings" class="tab-pane active">
    <div class="card" style="border-radius:16px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-gear me-2 text-primary"></i>{{ $isAdmin ? 'Global' : 'Restaurant' }} WhatsApp Configuration</span>
            <small class="text-muted">API v23.0 · Cloud API</small>
        </div>
        <div class="card-body">
            @if(!$isAdmin)<div class="alert alert-info small" style="border-radius:12px"><i class="bi bi-info-circle me-1"></i> Restaurant-level settings override global. Leave empty to use global. Webhook: <code>GET/POST {{ url('/api/whatsapp/webhook') }}</code> · Verify token must match.</div>@endif
            <form method="POST" action="{{ $route }}">@csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">WhatsApp Number <span class="text-muted fw-normal">(display)</span></label>
                        <input name="phone_number" value="{{ old('phone_number', $settings?->phone_number ?? '') }}" class="form-control" placeholder="+919876543210">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Phone Number ID <span class="text-danger">*</span></label>
                        <input name="phone_number_id" value="{{ old('phone_number_id', $settings?->phone_number_id ?? '') }}" class="form-control" placeholder="123456789012345">
                        <div class="form-text small">From Meta Developers → WhatsApp → API Setup</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Business Account ID</label>
                        <input name="business_account_id" value="{{ old('business_account_id', $settings?->business_account_id ?? '') }}" class="form-control" placeholder="102345678901234">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">App Secret <span class="text-muted fw-normal">(for webhook signature)</span></label>
                        <input name="app_secret" value="{{ old('app_secret', $settings?->app_secret ?? '') }}" type="password" class="form-control" placeholder="••••••••••••">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Access Token <span class="text-danger">*</span> <span class="text-muted fw-normal">— masked in UI, never logged</span></label>
                        <input name="access_token" value="" type="password" class="form-control" placeholder="{{ ($settings?->access_token) ? $masked($settings?->access_token).' (saved — leave blank to keep)' : 'EAAxxxx... (permanent token)' }}">
                        @if($settings?->access_token)<div class="form-text small text-success"><i class="bi bi-check-circle me-1"></i> Token saved ({{ strlen($settings?->access_token ?? '') }} chars, masked). Paste new to rotate.</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Webhook Verify Token</label>
                        <input name="webhook_verify_token" value="{{ old('webhook_verify_token', $settings?->webhook_verify_token ?? '') }}" class="form-control" placeholder="my_verify_token_123">
                        <div class="form-text small">Must match Meta webhook Verify Token &amp; <code>WHATSAPP_WEBHOOK_VERIFY_TOKEN</code></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">API Version</label>
                        <input name="api_version" value="{{ old('api_version', $settings?->api_version ?? 'v23.0') }}" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <label class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $settings?->is_enabled ?? false)) class="form-check-input" role="switch">
                            <span class="form-check-label small fw-semibold ms-2">Enabled</span>
                        </label>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Save &amp; Test</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ url('/api/whatsapp/webhook') }}'); this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy Webhook URL',1500)">Copy Webhook URL</button>
                </div>
                <div class="small text-muted mt-3">Webhook endpoint: <code>GET {{ url('/api/whatsapp/webhook') }}?hub_verify_token=...&amp;hub_challenge=...</code><br>Production: set <code>QUEUE_CONNECTION=database</code> + <code>php artisan queue:work</code> for incoming messages. Payloads stored in <code>whatsapp_webhooks</code> with idempotency guard.</div>
            </form>
        </div>
    </div>

    <div class="card mt-3" style="border-radius:16px; border:1px dashed #cce9e0; background:var(--primary-50)">
        <div class="card-body small">
            <div class="fw-semibold mb-1" style="color:var(--primary)"><i class="bi bi-shield-check me-1"></i> Production checklist</div>
            <ul class="mb-0 text-muted" style="line-height:1.7">
                <li>Tokens stored encrypted at rest (env or DB), never returned to frontend — masked above.</li>
                <li>Webhook verified via <code>hub_verify_token</code>; incoming payloads deduplicated by <code>event_id</code> unique.</li>
                <li>Messages queued via <code>SendWhatsAppMessage</code> job — survives rate limits.</li>
                <li>Set <code>WHATSAPP_API_VERSION</code>, <code>WHATSAPP_ACCESS_TOKEN</code> etc. in <code>.env</code> for global fallback.</li>
            </ul>
        </div>
    </div>
</div>

<div id="templates" class="tab-pane">
    <div class="card" style="border-radius:16px"><div class="card-header fw-semibold">Message Templates & Variables</div>
    <div class="card-body">
        <p class="small text-muted">Safe variable replacement via <code>TemplateVariableService</code> — unknown <code>{vars}</code> kept as-is. 24 vars: <code>{order_no}</code> <code>{customer_name}</code> <code>{grand_total}</code> <code>{track_order_url}</code> <code>{store_name}</code> etc. Full list in <code>app/Services/WhatsApp/TemplateVariableService.php:14</code>.</p>
        <div class="alert small" style="background:var(--primary-50); border:1px solid var(--primary-100); border-radius:12px">
            <div class="fw-semibold">Example — Order Confirmation</div>
            <pre class="mb-0 mt-2 p-3 bg-white border rounded small" style="white-space:pre-wrap">Thank you for your order!

Order No: {order_no}
Customer: {customer_name}
Items:
{item_name}
Subtotal: {sub_total}  Delivery: {delivery_charge}  Total: {grand_total}
Track: {track_order_url}</pre>
        </div>
        <div class="d-flex gap-2">
            <a href="#" class="btn btn-sm btn-outline-primary">Manage Templates</a>
            <span class="small text-muted align-self-center">Stored in <code>whatsapp_templates</code> + <code>whatsapp_automations</code> (event → template + delay).</span>
        </div>
    </div></div>
</div>

<div id="automation" class="tab-pane">
    <div class="card" style="border-radius:16px"><div class="card-header fw-semibold">Automation Rules (queued)</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="small text-muted" style="letter-spacing:.04em; text-transform:uppercase"><tr><th>Event</th><th>Template</th><th>Delay</th><th>Active</th></tr></thead>
                <tbody class="small">
                    @foreach(['welcome','order.created→Order Confirmation','order_accepted→Accepted','order_preparing→Preparing','order_ready→Ready','out_for_delivery→On the way','order_delivered→Delivered','order_cancelled→Cancelled','payment_successful','abandoned_cart (2h)'] as $ev)
                    <tr><td><code>{{ explode('→',$ev)[0] }}</code></td><td>{{ explode('→',$ev)[1] ?? '—' }}</td><td>{{ str_contains($ev,'abandoned')?'2h':'instant' }}</td><td><span class="badge bg-success">On</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="small text-muted mt-2">Handled by <code>WhatsAppAutomationService</code> listening to <code>OrderPlaced</code> / <code>OrderStatusChanged</code> → dispatches <code>SendWhatsAppMessage</code> (respecting <code>delay_seconds</code>).</div>
    </div></div>
</div>

<div id="conversations" class="tab-pane">
    <div class="card" style="border-radius:16px"><div class="card-header fw-semibold">Conversations & Handover</div>
    <div class="card-body">
        <p class="small text-muted">Vendor chat at <code>whatsapp_conversations</code> + <code>whatsapp_messages</code> (directions incoming/outgoing, statuses queued/sent/delivered/read/failed). Staff: <span class="badge bg-light text-dark border">Take Over</span> → AI stops; <span class="badge bg-light text-dark border">Return to AI</span> / <span class="badge bg-light text-dark border">Close</span>.</p>
        <div class="border rounded p-3 bg-light small" style="border-radius:12px">
            <div class="d-flex gap-2 mb-2"><span class="badge bg-white border text-dark">Customer: +91...</span> <span class="badge" style="background:#dcfce7; color:#166534">open · bot</span></div>
            <div class="bg-white border rounded p-2 mb-2" style="max-width:68%">Hi, is paneer biryani available today?</div>
            <div class="text-white rounded p-2 mb-2" style="background:var(--primary); max-width:68%; margin-left:auto">Yes! Paneer Biryani — ₹249. Would you like to order? <span class="small opacity-75 d-block mt-1">via AiConversationService (queued)</span></div>
        </div>
    </div></div>
</div>
</div>
@endsection