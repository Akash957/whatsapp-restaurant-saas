<?php

namespace App\Services\WhatsApp;

use App\Models\Order;

class TemplateVariableService
{
    public function render(string $template, Order $order): string
    {
        $vars = $this->variables($order);
        return preg_replace_callback('/\{(\w+)\}/', fn($m)=> $vars[$m[1]] ?? $m[0], $template);
    }

    public function variables(Order $order): array
    {
        $restaurant = $order->restaurant;
        $items = $order->items->map(fn($it)=> $it->name.' x'.$it->quantity.' ('.\App\Support\Money::format($it->total).')'.($it->variants->count()?' ['.$it->variants->pluck('name')->join(',').']':'').($it->addons->count()?' +'.$it->addons->pluck('name')->join(', '):''))->join("\n");
        return [
            'order_no'=>$order->order_number,'customer_name'=>$order->customer_name,'customer_mobile'=>$order->customer_mobile,'customer_email'=>$order->customer_email ?? '',
            'address'=>$order->address ?? '','building'=>$order->building ?? '','landmark'=>$order->landmark ?? '','postal_code'=>$order->postal_code ?? '',
            'item_name'=>$items,'qty'=>(string)$order->items->sum('quantity'),'item_price'=>\App\Support\Money::format($order->subtotal),
            'variantsdata'=>$items,'total'=>\App\Support\Money::format($order->total),'sub_total'=>\App\Support\Money::format($order->subtotal),
            'total_tax'=>\App\Support\Money::format($order->tax),'delivery_charge'=>\App\Support\Money::format($order->delivery_charge),
            'discount_amount'=>\App\Support\Money::format($order->discount),'grand_total'=>\App\Support\Money::format($order->total),
            'payment_type'=>$order->payment_method,'payment_status'=>$order->payment_status,'delivery_type'=>$order->order_type,'tips'=>\App\Support\Money::format($order->tips),'notes'=>$order->notes ?? '',
            'store_name'=>$restaurant->name ?? '','store_url'=>url('/restaurant/'.($restaurant->slug ?? '')),'track_order_url'=>url('/order/track/'.$order->tracking_token),
            'date'=>$order->created_at->format('d M Y'),'time'=>$order->created_at->format('H:i'),
        ];
    }
}