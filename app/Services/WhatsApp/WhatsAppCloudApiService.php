<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiService implements WhatsAppServiceInterface
{
    public function sendText(string $to, string $message, ?int $restaurantId = null): array
    {
        $cfg = $this->config($restaurantId);
        if (!$cfg || !$cfg->is_enabled) return ['queued'=>true,'reason'=>'WhatsApp disabled'];
        $url = "https://graph.facebook.com/{$cfg->api_version}/{$cfg->phone_number_id}/messages";
        $resp = Http::withToken($cfg->access_token)->post($url, ['messaging_product'=>'whatsapp','to'=>$to,'type'=>'text','text'=>['body'=>$message]]);
        if (!$resp->successful()) Log::warning('WA send failed', ['to'=>$to,'body'=>$resp->body()]);
        return $resp->json() ?? ['error'=>$resp->body()];
    }

    public function sendTemplate(string $to, string $templateName, array $params = [], ?int $restaurantId = null): array
    {
        $cfg = $this->config($restaurantId);
        if (!$cfg || !$cfg->is_enabled) return ['queued'=>true];
        $url = "https://graph.facebook.com/{$cfg->api_version}/{$cfg->phone_number_id}/messages";
        $resp = Http::withToken($cfg->access_token)->post($url, ['messaging_product'=>'whatsapp','to'=>$to,'type'=>'template','template'=>['name'=>$templateName,'language'=>['code'=>'en_US'],'components'=>$params]]);
        return $resp->json() ?? [];
    }

    public function markRead(string $messageId): bool { return true; }

    private function config(?int $restaurantId): ?WhatsAppSetting
    {
        if ($restaurantId) { $c = WhatsAppSetting::where('restaurant_id',$restaurantId)->first(); if($c) return $c; }
        return WhatsAppSetting::whereNull('restaurant_id')->first() ?? new WhatsAppSetting([
            'api_version'=>config('services.whatsapp.api_version','v23.0'),
            'phone_number_id'=>config('services.whatsapp.phone_number_id'),
            'access_token'=>config('services.whatsapp.access_token'),
            'is_enabled'=>filled(config('services.whatsapp.access_token')),
        ]);
    }
}