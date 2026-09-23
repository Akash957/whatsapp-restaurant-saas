<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Services\WhatsApp\WhatsAppAutomationService;
use App\Services\WhatsApp\WhatsAppMessageService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderWhatsAppNotification implements ShouldQueue
{
    public function __construct(private WhatsAppMessageService $msgSvc, private WhatsAppAutomationService $autoSvc) {}
    public function handle(OrderPlaced|OrderStatusChanged $event): void
    {
        $order = $event->order;
        if($event instanceof OrderPlaced){
            $this->msgSvc->sendOrderMessage($order, 'order.created');
            $this->autoSvc->handle('order.created', $order);
        } else {
            $map = ['confirmed'=>'order_accepted','preparing'=>'order_preparing','ready'=>'order_ready','out_for_delivery'=>'out_for_delivery','delivered'=>'order_delivered','cancelled'=>'order_cancelled'];
            $ev = $map[$order->status] ?? $order->status;
            $this->msgSvc->sendOrderMessage($order, $ev);
            $this->autoSvc->handle($ev, $order);
        }
    }
}