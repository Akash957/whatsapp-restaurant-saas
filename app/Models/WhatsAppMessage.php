<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends TenantModel
{
    protected $table = 'whatsapp_messages';

    protected function casts(): array
    {
        return ['raw_payload' => 'array', 'sent_at' => 'datetime'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'whatsapp_conversation_id');
    }
}