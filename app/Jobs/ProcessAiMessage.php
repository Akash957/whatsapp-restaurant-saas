<?php

namespace App\Jobs;

use App\Models\WhatsAppConversation;
use App\Services\Ai\AiConversationService;
use App\Services\WhatsApp\WhatsAppConversationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $conversationId, public string $text) {}
    public function handle(AiConversationService $ai, WhatsAppConversationService $convSvc): void
    {
        $conv = WhatsAppConversation::find($this->conversationId); if(!$conv || $conv->mode==='human') return;
        $reply = $ai->handleWhatsApp($conv, $this->text);
        if($reply) $convSvc->send($conv, $reply);
    }
}