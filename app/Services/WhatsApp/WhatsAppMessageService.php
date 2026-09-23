<?php

namespace App\Services\WhatsApp;

use App\Models\Order;
use App\Models\WhatsAppTemplate;

class WhatsAppMessageService
{
    public function __construct(private WhatsAppCloudApiService $api, private TemplateVariableService $vars) {}

    public function sendOrderMessage(Order $order, ?string $event = null): void
    {
        $event = $event ?? 'order.created';
        $templates = WhatsAppTemplate::where(function($q) use ($order){ $q->where('restaurant_id',$order->restaurant_id)->orWhereNull('restaurant_id'); })->where('event',$event)->where('is_enabled',true)->get();
        foreach($templates as $tpl){
            $body = $this->vars->render($tpl->body, $order);
            $this->api->sendText($order->customer_mobile, $body, $order->restaurant_id);
        }
    }
}