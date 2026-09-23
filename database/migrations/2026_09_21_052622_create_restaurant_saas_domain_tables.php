<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->index()->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
            $table->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 30)->default('pending')->index();
            $table->text('description')->nullable();
            $table->string('email');
            $table->string('phone', 30);
            $table->string('whatsapp_number', 30)->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');
            $table->string('postal_code', 20);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('banner')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color', 20)->default('#16876b');
            $table->string('secondary_color', 20)->default('#f1f8f5');
            $table->string('button_color', 20)->default('#16876b');
            $table->string('text_color', 20)->default('#18392b');
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->text('custom_footer')->nullable();
            $table->unsignedTinyInteger('onboarding_step')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Existing users need a valid default before the role foreign key is added.
        DB::table('roles')->insert([
            'name' => 'customer',
            'label' => 'Customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('role')->default('customer')->index();
            $table->foreign('role')->references('name')->on('roles')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('restaurant_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->string('key', 100);
            $table->json('value')->nullable();
            $table->timestamps();
            $table->unique(['restaurant_id', 'key']);
        });

        Schema::create('restaurant_business_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day');
            $table->boolean('is_closed')->default(false);
            $table->time('opens_at')->default('10:00:00');
            $table->time('closes_at')->default('22:00:00');
            $table->timestamps();
            $table->unique(['restaurant_id', 'day']);
        });

        Schema::create('restaurant_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['restaurant_id', 'slug']);
            $table->index(['restaurant_id', 'is_active', 'sort_order']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->index()->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->string('sku', 100)->nullable();
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['restaurant_id', 'slug']);
            $table->index(['restaurant_id', 'is_available', 'sort_order']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'name']);
        });

        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('max_selections')->default(5);
            $table->timestamps();
            $table->unique(['product_id', 'name']);
        });

        Schema::create('product_addon_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_addon_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['product_addon_id', 'name']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('mobile', 30);
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['restaurant_id', 'mobile']);
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->text('address');
            $table->string('building')->nullable();
            $table->string('landmark')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->index()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('delivery_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enable_delivery')->default(true);
            $table->boolean('enable_pickup')->default(true);
            $table->boolean('enable_dine_in')->default(true);
            $table->unsignedBigInteger('minimum_order')->default(0);
            $table->unsignedBigInteger('delivery_charge')->default(0);
            $table->unsignedBigInteger('free_delivery_above')->nullable();
            $table->decimal('delivery_radius', 8, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(5);
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('type', 30);
            $table->decimal('value', 14, 2);
            $table->unsignedBigInteger('min_order')->default(0);
            $table->unsignedBigInteger('max_discount')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_customer_limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['restaurant_id', 'code']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('order_number', 50)->unique();
            $table->uuid('tracking_token')->unique();
            $table->string('status', 30)->default('pending');
            $table->string('payment_method', 30);
            $table->string('payment_status', 30)->default('pending');
            $table->string('order_type', 30);
            $table->string('customer_name');
            $table->string('customer_mobile', 30);
            $table->string('customer_email')->nullable();
            $table->text('address')->nullable();
            $table->string('building')->nullable();
            $table->string('landmark')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('delivery_charge')->default(0);
            $table->unsignedBigInteger('tips')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->foreignId('coupon_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->uuid('idempotency_key')->unique();
            $table->timestamps();
            $table->index(['restaurant_id', 'status', 'created_at']);
            $table->index(['restaurant_id', 'created_at']);
            $table->index(['restaurant_id', 'payment_status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('total');
            $table->timestamps();
        });

        Schema::create('order_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->timestamps();
        });

        Schema::create('order_item_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('product_addon_item_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('status', 30);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'created_at']);
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('discount');
            $table->timestamps();
            $table->index(['coupon_id', 'customer_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('provider', 30);
            $table->string('provider_order_id')->nullable()->unique();
            $table->string('provider_payment_id')->nullable()->unique();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('INR');
            $table->string('status', 30)->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->index(['restaurant_id', 'status']);
        });

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('duration_days')->default(30);
            $table->text('description')->nullable();
            $table->unsignedInteger('max_products')->default(50);
            $table->unsignedInteger('max_orders')->default(500);
            $table->unsignedInteger('max_staff')->default(3);
            $table->unsignedInteger('whatsapp_messages')->default(1000);
            $table->boolean('ai_automation')->default(false);
            $table->boolean('custom_domain')->default(false);
            $table->boolean('white_label')->default(false);
            $table->boolean('reports')->default(true);
            $table->string('support')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->index()->constrained()->restrictOnDelete();
            $table->string('status', 30)->default('trial');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->index(['restaurant_id', 'status', 'ends_at']);
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->index()->constrained()->cascadeOnDelete();
            $table->string('provider', 30);
            $table->string('provider_order_id')->nullable()->unique();
            $table->string('provider_payment_id')->nullable()->unique();
            $table->unsignedBigInteger('amount');
            $table->string('status', 30)->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->index(['restaurant_id', 'status']);
        });

        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->string('phone_number', 30)->nullable();
            $table->string('phone_number_id')->nullable();
            $table->string('business_account_id')->nullable();
            $table->text('access_token')->nullable();
            $table->text('webhook_verify_token')->nullable();
            $table->text('app_secret')->nullable();
            $table->string('api_version', 20)->default('v23.0');
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('event', 100)->nullable();
            $table->string('title')->nullable();
            $table->text('body');
            $table->string('language', 20)->default('en');
            $table->string('meta_template_name')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['restaurant_id', 'name']);
            $table->index(['restaurant_id', 'event']);
        });

        Schema::create('whatsapp_automations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('event', 100);
            $table->foreignId('whatsapp_template_id')->index()->constrained('whatsapp_templates')->cascadeOnDelete();
            $table->unsignedInteger('delay_seconds')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['restaurant_id', 'name']);
            $table->index(['restaurant_id', 'event', 'is_active']);
        });

        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('mobile', 30);
            $table->string('status', 30)->default('open');
            $table->string('mode', 30)->default('bot');
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->json('state')->nullable();
            $table->timestamps();
            $table->unique(['restaurant_id', 'mobile']);
            $table->index(['restaurant_id', 'status', 'last_message_at']);
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_conversation_id')->index()->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->string('message_id')->nullable()->unique();
            $table->string('direction', 20);
            $table->string('type', 30)->default('text');
            $table->text('content');
            $table->string('status', 30)->default('queued');
            $table->json('raw_payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['whatsapp_conversation_id', 'created_at'], 'whatsapp_messages_conversation_created_index');
            $table->index(['restaurant_id', 'status']);
        });

        Schema::create('whatsapp_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->foreignId('restaurant_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->json('payload');
            $table->string('status', 30)->default('pending')->index();
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_conversation_id')->index()->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->string('status', 30)->default('open');
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('ai_conversation_id')->index()->constrained()->cascadeOnDelete();
            $table->string('role', 30);
            $table->text('content');
            $table->timestamps();
            $table->index(['ai_conversation_id', 'created_at']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('module', 100);
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['restaurant_id', 'module', 'created_at']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('mobile', 30)->nullable();
            $table->json('items');
            $table->string('coupon_code', 50)->nullable();
            $table->timestamp('last_activity_at');
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();
            $table->index(['restaurant_id', 'user_id']);
            $table->index(['restaurant_id', 'last_activity_at', 'reminded_at']);
        });
    }

    public function down(): void
    {
        foreach ([
            'carts', 'settings', 'activity_logs', 'notifications', 'ai_messages',
            'ai_conversations', 'whatsapp_webhooks', 'whatsapp_messages',
            'whatsapp_conversations', 'whatsapp_automations', 'whatsapp_templates',
            'whatsapp_settings', 'subscription_payments', 'subscriptions',
            'subscription_plans', 'payments', 'coupon_usages', 'order_status_histories',
            'order_item_addons', 'order_item_variants', 'order_items', 'orders',
            'coupons', 'delivery_settings', 'favorites', 'customer_addresses',
            'customers', 'product_addon_items', 'product_addons', 'product_variants',
            'products', 'categories', 'restaurant_domains', 'restaurant_business_hours',
            'restaurant_settings', 'restaurant_staff',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['role']);
            $table->dropIndex(['restaurant_id']);
            $table->dropIndex(['role']);
            $table->dropColumn(['restaurant_id', 'role', 'phone', 'is_active']);
        });

        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
