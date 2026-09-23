<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'duration_days' => 'integer',
            'max_products' => 'integer',
            'max_orders' => 'integer',
            'max_staff' => 'integer',
            'whatsapp_messages' => 'integer',
            'ai_automation' => 'boolean',
            'custom_domain' => 'boolean',
            'white_label' => 'boolean',
            'reports' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}