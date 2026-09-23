<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends TenantModel
{
    protected function casts(): array
    {
        return ['items' => 'array', 'last_activity_at' => 'datetime', 'reminded_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}