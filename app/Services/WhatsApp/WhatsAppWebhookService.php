<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppWebhook;
use Illuminate\Support\Str;

class WhatsAppWebhookService
{
    public function verify(array $query, string $verifyToken): ?string
    {
        if(($query['hub_verify_token'] ?? null) === $verifyToken) return $query['hub_challenge'] ?? null;
        return null;
    }

    public function handle(array $payload): void
    {
        $eventId = $payload['entry'][0]['id'] ?? Str::uuid();
        // Idempotency: skip duplicates
        if(WhatsAppWebhook::where('event_id',$eventId)->exists()) return;
        $record = WhatsAppWebhook::create(['event_id'=>(string)Str::uuid(),'payload'=>$payload,'status'=>'pending']);
        try {
            foreach($payload['entry'] ?? [] as $entry){
                foreach($entry['changes'] ?? [] as $change){
                    $value = $change['value'] ?? [];
                    foreach($value['messages'] ?? [] as $msg){
                        $from = $msg['from'] ?? null; $text = $msg['text']['body'] ?? '';
                        if(!$from) continue;
                        // Find restaurant by phone_number_id
                        $phoneId = $value['metadata']['phone_number_id'] ?? null;
                        $setting = $phoneId ? \App\Models\WhatsAppSetting::where('phone_number_id',$phoneId)->first() : null;
                        $restaurantId = $setting->restaurant_id ?? \App\Models\Restaurant::where('status','active')->value('id');
                        if(!$restaurantId) continue;
                        $conv = WhatsAppConversation::firstOrCreate(['restaurant_id'=>$restaurantId,'mobile'=>$from],['status'=>'open','mode'=>'bot','last_message'=>$text,'last_message_at'=>now()]);
                        $conv->update(['last_message'=>$text,'last_message_at'=>now()]);
                        WhatsAppMessage::create(['restaurant_id'=>$restaurantId,'whatsapp_conversation_id'=>$conv->id,'message_id'=>$msg['id'] ?? Str::uuid(),'direction'=>'incoming','type'=>$msg['type'] ?? 'text','content'=>$text,'status'=>'received','raw_payload'=>$msg]);
                    }
                    foreach($value['statuses'] ?? [] as $st){
                        WhatsAppMessage::where('message_id',$st['id'] ?? '')->update(['status'=>$st['status'] ?? 'sent']);
                    }
                }
            }
            $record->update(['status'=>'processed','processed_at'=>now()]);
        } catch(\Throwable $e){ $record->update(['status'=>'failed','error'=>$e->getMessage()]); }
    }
}