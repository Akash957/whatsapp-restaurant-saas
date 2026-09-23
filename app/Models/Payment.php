<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends TenantModel
{
    protected function casts(): array
    {
        return ['amount' => 'integer', 'payload' => 'array'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}