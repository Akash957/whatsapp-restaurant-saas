<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantDomain extends TenantModel
{
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}