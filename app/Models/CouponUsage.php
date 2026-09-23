<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends TenantModel
{
    protected function casts(): array
    {
        return ['discount' => 'integer'];
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}