<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function createOrder(Order $order): array;
    public function verifyWebhook(array $payload, string $signature): bool;
    public function capture(string $paymentId, int $amount): array;
    public function refund(Order $order, ?int $amount = null): array;
}