<?php

namespace App\Services\WhatsApp;

interface WhatsAppServiceInterface
{
    public function sendText(string $to, string $message, ?int $restaurantId = null): array;
    public function sendTemplate(string $to, string $templateName, array $params = [], ?int $restaurantId = null): array;
    public function markRead(string $messageId): bool;
}