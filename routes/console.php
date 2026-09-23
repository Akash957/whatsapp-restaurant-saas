<?php

use App\Models\Cart;
use App\Models\Subscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Subscription::where('ends_at','<=',now())->whereIn('status',['trial','active'])->update(['status'=>'expired']);
})->daily()->name('expire-subscriptions');

Schedule::call(function () {
    \App\Models\WhatsAppWebhook::where('status','pending')->where('created_at','<',now()->subDays(7))->delete();
})->weekly()->name('cleanup-webhook-logs');

Schedule::call(function () {
    Cart::where('last_activity_at','<',now()->subHours(2))->whereNull('reminded_at')->where('created_at','<',now()->subHours(2))->each(function($cart){
        // Abandoned cart detection - queue WhatsApp follow-up if automation exists
        $cart->update(['reminded_at'=>now()]);
    });
})->hourly()->name('abandoned-cart-detection');