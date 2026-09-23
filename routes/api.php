<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::prefix('v1')->group(function () {
    Route::get('/whatsapp/webhook', [ApiController::class, 'webhookVerify']);
    Route::post('/whatsapp/webhook', [ApiController::class, 'webhookReceive']);
    Route::post('/payments/razorpay/webhook', [ApiController::class, 'razorpayWebhook']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/restaurants', [ApiController::class, 'restaurants']);
        Route::get('/restaurants/{restaurant}', [ApiController::class, 'showRestaurant']);
        Route::get('/categories', [ApiController::class, 'categories']);
        Route::get('/products', [ApiController::class, 'products']);
        Route::get('/orders/{order}', [ApiController::class, 'orderDetail']);
    });
});