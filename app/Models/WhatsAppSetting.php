<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSetting extends Model
{
    protected $table = 'whatsapp_settings';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}