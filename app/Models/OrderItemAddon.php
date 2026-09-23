<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemAddon extends TenantModel
{
    protected function casts(): array
    {
        return ['price' => 'integer'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productAddonItem(): BelongsTo
    {
        return $this->belongsTo(ProductAddonItem::class);
    }
}