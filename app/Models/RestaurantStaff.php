<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantStaff extends TenantModel
{
    protected function casts(): array
    {
        return ['permissions' => 'array'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}