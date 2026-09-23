<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAddonItem extends TenantModel
{
    protected function casts(): array
    {
        return ['price' => 'integer', 'is_available' => 'boolean'];
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(ProductAddon::class, 'product_addon_id');
    }
}