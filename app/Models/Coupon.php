<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends TenantModel
{
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order' => 'integer',
            'max_discount' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'usage_limit' => 'integer',
            'per_customer_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
