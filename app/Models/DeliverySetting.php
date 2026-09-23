<?php

namespace App\Models;

class DeliverySetting extends TenantModel
{
    protected function casts(): array
    {
        return [
            'enable_delivery' => 'boolean',
            'enable_pickup' => 'boolean',
            'enable_dine_in' => 'boolean',
            'minimum_order' => 'integer',
            'delivery_charge' => 'integer',
            'free_delivery_above' => 'integer',
            'delivery_radius' => 'decimal:2',
            'tax_rate' => 'decimal:2',
        ];
    }
}
