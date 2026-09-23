<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends TenantModel
{
    protected function casts(): array
    {
        return ['amount' => 'integer', 'payload' => 'array'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}