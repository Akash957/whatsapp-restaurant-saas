<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Policies\OrderPolicy;
use App\Policies\RestaurantPolicy;
use App\Policies\TenantPolicy;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderWhatsAppNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Restaurant::class, RestaurantPolicy::class);
        Gate::policy(Category::class, TenantPolicy::class);
        Gate::policy(Product::class, TenantPolicy::class);
        Event::listen(OrderPlaced::class, SendOrderWhatsAppNotification::class);
        Event::listen(OrderStatusChanged::class, SendOrderWhatsAppNotification::class);
    }
}