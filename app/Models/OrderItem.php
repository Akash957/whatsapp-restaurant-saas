<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends TenantModel
{
    protected function casts(): array
    {
        return ['quantity' => 'integer', 'unit_price' => 'integer', 'total' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(OrderItemVariant::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(OrderItemAddon::class);
    }
}