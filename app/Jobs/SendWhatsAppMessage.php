<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsApp\TemplateVariableService;
use App\Services\WhatsApp\WhatsAppCloudApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $orderId, public int $templateId) {}
    public function handle(WhatsAppCloudApiService $api, TemplateVariableService $vars): void
    {
        $order = Order::with('restaurant','items')->find($this->orderId); if(!$order) return;
        $tpl = WhatsAppTemplate::find($this->templateId); if(!$tpl) return;
        $body = $vars->render($tpl->body, $order);
        $api->sendText($order->customer_mobile, $body, $order->restaurant_id);
    }
}