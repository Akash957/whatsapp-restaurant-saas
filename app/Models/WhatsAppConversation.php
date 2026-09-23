<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends TenantModel
{
    protected $table = 'whatsapp_conversations';

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime', 'state' => 'array'];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }
}