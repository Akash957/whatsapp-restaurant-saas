# WhatsApp Restaurant SaaS

Multi-restaurant SaaS platform where restaurants manage menus, orders, customers, and WhatsApp ordering. Built with Laravel 13, PHP 8.3, MySQL 8, Blade + Bootstrap 5, Sanctum, Queue, Scheduler, WhatsApp Business Cloud API, and Razorpay-ready payments.

> Inspired by Restro SaaS screenshots but all code is original — no CodeCanyon/copyrighted assets.

## Requirements

- PHP 8.3+
- Composer 2
- MySQL 8 / SQLite for testing
- Node 20+ (optional, for Vite)
- Extensions: `ext-bcmath`, `ext-gd`, `ext-intl` recommended

## Installation

```bash
git clone <repo> whatsapp-restaurant-saas
cd whatsapp-restaurant-saas
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env`:

```
APP_NAME="WhatsApp Restaurant SaaS"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whatsapp_restaurant_saas
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

WHATSAPP_API_VERSION=v23.0
WHATSAPP_ACCESS_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_WEBHOOK_VERIFY_TOKEN=
WHATSAPP_APP_SECRET=

RAZORPAY_KEY=
RAZORPAY_SECRET=
RAZORPAY_MODE=test
RAZORPAY_WEBHOOK_SECRET=

AI_PROVIDER=openai
AI_API_KEY=
AI_MODEL=gpt-4o-mini
AI_BASE_URL=
```

```bash
php artisan migrate --seed
php artisan storage:link
php artisan optimize:clear
```

### Demo Accounts (seeded)

| Role | Email | Password | Notes |
|------|-------|----------|-------|
| Super Admin | admin@example.com | password | All restaurants |
| Vendor | vendor@example.com | password | Green Food Restaurant (active) |
| Customer | customer@example.com | password | — |

Demo restaurant slug: `green-food-restaurant` → `/restaurant/green-food-restaurant`

## Running

```bash
php artisan serve          # http://localhost:8000
php artisan queue:work     # WhatsApp / emails
php artisan schedule:work  # or cron: * * * * * php artisan schedule:run
npm install && npm run dev # Vite (Tailwind + app.js)
npm run build              # production assets
```

## Key Routes

- `/` — SaaS landing + live restaurants + pricing
- `/register` — Restaurant registration (pending → admin approves)
- `/login` — Unified login (role-based redirect)
- `/admin/*` — Super admin (restaurants, orders, plans, reports)
- `/vendor/*` — Restaurant owner (menu, orders, WhatsApp, QR, subscription)
- `/staff/*` — Staff (orders, customers)
- `/customer/*` — Customer portal (orders, profile)
- `/restaurant/{slug}`, `/menu/{slug}`, `/product/{slug}/{product}`, `/cart/{slug}`, `/checkout/{slug}`, `/order/track/{token}`
- `GET/POST /api/v1/whatsapp/webhook` — WhatsApp webhook (verify + receive)
- `POST /api/v1/payments/razorpay/webhook` — Razorpay webhook

## Architecture Highlights

- **Multi-tenant isolation:** `restaurant_id` on tenant tables, `TenantMiddleware` + `TenantPolicy`/`RestaurantPolicy`/`OrderPolicy`, query scopes. Vendors never see other vendors' data.
- **Money:** all prices stored as integer paise, displayed via `App\Support\Money::format()`. `CartService::quote()` and `OrderService::create()` recalculate server-side; frontend prices are never trusted. DB transactions + `lockForUpdate()` for orders/payments.
- **Orders:** `ORD-YYYY-000001` + `tracking_token` (uuid) + `idempotency_key`. Statuses `pending→confirmed→preparing→ready→out_for_delivery→delivered` (cancelled allowed early). History in `order_status_histories` + `OrderStatusChanged` event.
- **WhatsApp:** `WhatsAppServiceInterface` → `WhatsAppCloudApiService` (Graph API), `TemplateVariableService` safely replaces `{vars}`, `WhatsAppAutomationService` dispatches queued `SendWhatsAppMessage` jobs on `OrderPlaced`/`OrderStatusChanged`. Webhook deduplication via `event_id`, raw payload stored. Conversations (`whatsapp_conversations`/`whatsapp_messages`) with `bot|human` mode + handover. AI via `AiConversationService` (configurable provider, queued `ProcessAiMessage`).
- **Payments:** `PaymentGatewayInterface` → `RazorpayPaymentService`; COD always available. Never store card data. Webhook signature verified. `payments`/`subscription_payments` tables.
- **Subscriptions:** `subscription_plans` (Basic/Professional/Enterprise) → `subscriptions` (trial/active/expired/cancelled/suspended). Enforced in `OrderService::create()` (monthly limit check).
- **Security:** CSRF, XSS, mass-assignment (`$guarded`), `FormRequest` validation, `TenantMiddleware`/`EnsureRole`/`SecurityHeaders`, rate limiting, encrypted tokens not logged/masked in UI, queue + scheduler for heavy work.
- **Performance:** Eager loading, indexes, pagination, caching, queued WhatsApp/emails/AI.

## WhatsApp Setup

1. Create app at developers.facebook.com → WhatsApp Business → Cloud API.
2. Copy Phone Number ID, Business Account ID, Access Token → `.env`.
3. Set webhook: `GET https://your-domain/api/v1/whatsapp/webhook?hub_verify_token=...&hub_challenge=...` and `POST` for events; verify token must match `WHATSAPP_WEBHOOK_VERIFY_TOKEN`.
4. Restaurant-level settings in `whatsapp_settings` override global `.env`.

## Razorpay Setup

1. Create account, get Key/Secret → `.env`.
2. Webhook: `POST https://your-domain/api/v1/payments/razorpay/webhook` with `RAZORPAY_WEBHOOK_SECRET`.
3. Orders use `cod` (always) or `razorpay`; online path calls `RazorpayPaymentService::createOrder()` and waits for webhook to mark `paid`.

## AI Setup

Set `AI_PROVIDER`, `AI_API_KEY`, `AI_MODEL` in `.env`. Supported provider is OpenAI-compatible (base_url configurable). AI never mutates orders/payments directly — validated server-side.

## Testing

```bash
php artisan test
# key tests: tenant isolation (vendor A cannot see vendor B), cart/quote, order creation/status, coupon, webhook idempotency
```

## Production Deployment

- `APP_ENV=production APP_DEBUG=false`, `php artisan optimize`, queue via Supervisor, scheduler via cron, storage link, HTTPS.
- Never commit real credentials. Use env/vault.
