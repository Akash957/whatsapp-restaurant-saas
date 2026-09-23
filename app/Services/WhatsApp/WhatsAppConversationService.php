<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;

class WhatsAppConversationService
{
    public function __construct(private WhatsAppCloudApiService $api) {}

    public function send(WhatsAppConversation $conv, string $text, string $type='text'): WhatsAppMessage
    {
        $msg = WhatsAppMessage::create(['restaurant_id'=>$conv->restaurant_id,'whatsapp_conversation_id'=>$conv->id,'direction'=>'outgoing','type'=>$type,'content'=>$text,'status'=>'queued']);
        $result = $this->api->sendText($conv->mobile, $text, $conv->restaurant_id);
        $msg->update(['status'=> isset($result['error']) ? 'failed' : 'sent','raw_payload'=>$result,'sent_at'=>now()]);
        $conv->update(['last_message'=>$text,'last_message_at'=>now()]);
        return $msg;
    }

    public function takeOver(WhatsAppConversation $conv): void { $conv->update(['mode'=>'human']); }
    public function returnToBot(WhatsAppConversation $conv): void { $conv->update(['mode'=>'bot']); }
    public function close(WhatsAppConversation $conv): void { $conv->update(['status'=>'closed']); }
}