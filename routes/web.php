<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelResourceController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\RestaurantSettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\StorefrontController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the Laravel ServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/restaurant', [AuthController::class, 'register'])->name('restaurant.register');
Route::get('/register/customer', [AuthController::class, 'showCustomerRegister'])->name('customer.register');
Route::post('/customer', [AuthController::class, 'registerCustomer'])->name('customer.register.post');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/reset-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');
Route::get('/verify-email', [AuthController::class, 'verificationNotice'])->name('verification.notice');
Route::post('/send-verification', [AuthController::class, 'sendVerification'])->name('verification.send');
Route::get('/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:super_admin'])
    ->name('admin.')
    ->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/restaurants', [DashboardController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/{restaurant}/approve', [DashboardController::class, 'approve'])->name('restaurant.approve');
    Route::get('/restaurants/{restaurant}/reject', [DashboardController::class, 'reject'])->name('restaurant.reject');
    Route::get('/restaurants/{restaurant}/activate', [DashboardController::class, 'activate'])->name('restaurant.activate');
    Route::get('/restaurants/{restaurant}/deactivate', [DashboardController::class, 'deactivate'])->name('restaurant.deactivate');
    Route::get('/restaurant-staff', [DashboardController::class, 'staff'])->name('staff');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('customers');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/products', [DashboardController::class, 'products'])->name('products');
    Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
    Route::get('/subscriptions', [DashboardController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/payments', [DashboardController::class, 'payments'])->name('payments');
    Route::get('/whatsapp', [DashboardController::class, 'whatsapp'])->name('whatsapp');
    Route::post('/whatsapp', [DashboardController::class, 'updateWhatsapp'])->name('whatsapp.update');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/website-settings', [DashboardController::class, 'websiteSettings'])->name('website-settings');
    Route::get('/system-settings', [DashboardController::class, 'systemSettings'])->name('system-settings');
});

/*
|--------------------------------------------------------------------------
| Vendor / Restaurant Routes
|--------------------------------------------------------------------------
*/

Route::prefix('vendor')
    ->middleware(['auth', 'role:vendor', 'tenant'])
    ->name('vendor.')
    ->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'vendorDashboard'])->name('dashboard');
    Route::get('/restaurant-profile', [DashboardController::class, 'restaurantProfile'])->name('profile');
    Route::put('/restaurant-profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/business-hours', [DashboardController::class, 'businessHours'])->name('business-hours');
    Route::post('/business-hours', [DashboardController::class, 'updateBusinessHours'])->name('business-hours.update');
    Route::get('/categories', [PanelResourceController::class, 'categories'])->name('categories');
    Route::post('/categories', [PanelResourceController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [PanelResourceController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [PanelResourceController::class, 'deleteCategory'])->name('categories.delete');
    Route::get('/products', [PanelResourceController::class, 'products'])->name('products');
    Route::post('/products', [PanelResourceController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [PanelResourceController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [PanelResourceController::class, 'deleteProduct'])->name('products.delete');
    Route::get('/variants', [PanelResourceController::class, 'variants'])->name('variants');
    Route::post('/variants', [PanelResourceController::class, 'storeVariant'])->name('variants.store');
    Route::get('/add-ons', [PanelResourceController::class, 'addons'])->name('addons');
    Route::post('/add-ons', [PanelResourceController::class, 'storeAddon'])->name('addons.store');
    Route::get('/orders', [OrderManagementController::class, 'vendorOrders'])->name('orders');
    Route::get('/orders/{order}', [OrderManagementController::class, 'orderDetail'])->name('order.detail');
    Route::put('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('order.status.update');
    Route::get('/coupons', [PanelResourceController::class, 'coupons'])->name('coupons');
    Route::post('/coupons', [PanelResourceController::class, 'storeCoupon'])->name('coupons.store');
    Route::put('/coupons/{coupon}', [PanelResourceController::class, 'updateCoupon'])->name('coupons.update');
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');
    Route::put('/subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
    Route::get('/whatsapp', [DashboardController::class, 'whatsapp'])->name('whatsapp');
    Route::post('/whatsapp', [DashboardController::class, 'updateWhatsapp'])->name('whatsapp.update');
    Route::get('/qr-code', [DashboardController::class, 'qrCode'])->name('qr.code');
    Route::get('/settings', [RestaurantSettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [RestaurantSettingsController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/

Route::prefix('staff')
    ->middleware(['auth', 'role:staff', 'tenant'])
    ->name('staff.')
    ->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard');
    Route::get('/orders', [OrderManagementController::class, 'staffOrders'])->name('orders');
    Route::put('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('order.status.update');
    Route::get('/customers', [DashboardController::class, 'staffCustomers'])->name('customers');
    Route::get('/whatsapps', [DashboardController::class, 'whatsapp'])->name('whatsapp');
});

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('customer')
    ->middleware(['auth', 'role:customer'])
    ->name('customer.')
    ->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [CustomerController::class, 'orderDetail'])->name('order.detail');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Storefront (Customer-facing) Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    try {
        $restaurants = \App\Models\Restaurant::where('status','active')->latest()->limit(12)->get();
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
    } catch (\Throwable $e) {
        $restaurants = collect(); $plans = collect();
    }
    return view('welcome', compact('restaurants','plans'));
})->name('store.home');
Route::get('/restaurant/{restaurant}', [StorefrontController::class, 'home'])->name('restaurant.home');
Route::get('/menu/{restaurant}', [StorefrontController::class, 'menu'])->name('menu');
Route::get('/product/{restaurant}/{product}', [StorefrontController::class, 'product'])->name('product');
Route::get('/about/{restaurant}', [StorefrontController::class, 'about'])->name('about');
Route::get('/contact/{restaurant}', [StorefrontController::class, 'contact'])->name('contact');
Route::get('/cart/{restaurant}', [StorefrontController::class, 'cart'])->name('cart');
Route::post('/cart/{restaurant}/add', [StorefrontController::class, 'add'])->name('cart.add');
Route::post('/cart/{restaurant}/update/{line}', [StorefrontController::class, 'update'])->name('cart.update');
Route::post('/cart/{restaurant}/remove/{line}', [StorefrontController::class, 'remove'])->name('cart.remove');
Route::post('/cart/{restaurant}/coupon', [StorefrontController::class, 'coupon'])->name('coupon.apply');
Route::get('/checkout/{restaurant}', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/{restaurant}', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/success/{order}', [StorefrontController::class, 'success'])->name('order.success');
Route::get('/order/track/{tracking_token}', [StorefrontController::class, 'track'])->name('order.track');

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1')
    ->middleware(['auth:sanctum'])
    ->name('api.')
    ->group(function () {
    Route::post('/login', [ApiController::class, 'login'])->name('login');
    Route::post('/register', [ApiController::class, 'register'])->name('register');
    Route::get('/restaurants', [ApiController::class, 'index'])->name('restaurants');
    Route::get('/restaurants/{restaurant}', [ApiController::class, 'show'])->name('restaurant');
    Route::get('/categories', [ApiController::class, 'categories'])->name('categories');
    Route::get('/categories/{category}', [ApiController::class, 'categoryProducts'])->name('category.products');
    Route::get('/products', [ApiController::class, 'products'])->name('products');
    Route::get('/products/{product}', [ApiController::class, 'productDetail'])->name('product.detail');
    Route::post('/cart/add', [ApiController::class, 'cartAdd'])->name('cart.add');
    Route::post('/cart/remove/{line}', [ApiController::class, 'cartRemove'])->name('cart.remove');
    Route::post('/cart/coupon', [ApiController::class, 'coupon'])->name('coupon.apply');
    Route::post('/checkout', [ApiController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{order}', [ApiController::class, 'orderDetail'])->name('order.detail');
    Route::get('/payments', [ApiController::class, 'payments'])->name('payments');
    Route::post('/whatsapp', [ApiController::class, 'whatsapp'])->name('whatsapp');
    Route::get('/subscriptions', [ApiController::class, 'subscriptions'])->name('subscriptions');
});

/*
|--------------------------------------------------------------------------
| WhatsApp API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1/whatsapp')
    ->middleware(['auth:sanctum'])
    ->name('whatsapp.')
    ->group(function () {
    Route::get('/webhook', [ApiController::class, 'webhookVerify'])->name('webhook.verify');
    Route::post('/webhook', [ApiController::class, 'webhookReceive'])->name('webhook.receive');
    Route::get('/templates', [ApiController::class, 'templates'])->name('templates');
    Route::post('/templates', [ApiController::class, 'storeTemplate'])->name('templates.store');
    Route::get('/automations', [ApiController::class, 'automations'])->name('automations');
    Route::post('/automations', [ApiController::class, 'storeAutomation'])->name('automations.store');
    Route::get('/conversations', [ApiController::class, 'conversations'])->name('conversations');
    Route::post('/conversations', [ApiController::class, 'startConversation'])->name('conversations.start');
});

/*
|--------------------------------------------------------------------------
| Catch-all 404
|--------------------------------------------------------------------------
*/

Route::get('{any}', function () {
    abort(404);
})->where('any', '.*');