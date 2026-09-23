<?php

namespace App\Jobs;

use App\Services\WhatsApp\WhatsAppWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWhatsAppWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public array $payload) {}
    public function handle(WhatsAppWebhookService $svc): void { $svc->handle($this->payload); }
}