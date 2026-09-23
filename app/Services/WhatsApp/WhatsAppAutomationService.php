<?php

namespace App\Services\WhatsApp;

use App\Models\Order;
use App\Models\WhatsAppAutomation;
use App\Jobs\SendWhatsAppMessage;

class WhatsAppAutomationService
{
    public function handle(string $event, Order $order): void
    {
        $autos = WhatsAppAutomation::where('restaurant_id',$order->restaurant_id)->where('event',$event)->where('is_active',true)->with('template')->get();
        foreach($autos as $auto){
            $job = new SendWhatsAppMessage($order->id, $auto->whatsapp_template_id);
            if($auto->delay_seconds > 0) $job->delay(now()->addSeconds($auto->delay_seconds));
            dispatch($job);
        }
    }
}