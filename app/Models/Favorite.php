<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends TenantModel
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}