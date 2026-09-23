<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    public const EVENTS = [
        'welcome', 'new_order', 'order_confirmation', 'order_accepted', 'order_preparing',
        'order_ready', 'out_for_delivery', 'order_delivered', 'order_cancelled',
        'payment_successful', 'payment_failed', 'abandoned_cart', 'customer_followup',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}