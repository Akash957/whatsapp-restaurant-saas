<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantSetting extends TenantModel
{
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}