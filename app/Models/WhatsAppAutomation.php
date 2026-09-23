<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppAutomation extends TenantModel
{
    protected $table = 'whatsapp_automations';

    protected function casts(): array
    {
        return ['delay_seconds' => 'integer', 'is_active' => 'boolean'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'whatsapp_template_id');
    }
}