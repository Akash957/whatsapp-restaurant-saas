<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayPaymentService implements PaymentGatewayInterface
{
    public function createOrder(Order $order): array
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        if (!$key || !$secret) throw new \RuntimeException('Razorpay not configured');

        $payload = ['amount'=>$order->total,'currency'=>'INR','receipt'=>$order->order_number,'notes'=>['order_id'=>$order->id]];
        $response = Http::withBasicAuth($key, $secret)->post('https://api.razorpay.com/v1/orders', $payload);
        if (!$response->successful()) { Log::error('Razorpay create failed', ['body'=>$response->body()]); throw new \RuntimeException('Razorpay order failed'); }
        $data = $response->json();
        Payment::create(['restaurant_id'=>$order->restaurant_id,'order_id'=>$order->id,'provider'=>'razorpay','provider_order_id'=>$data['id'] ?? null,'amount'=>$order->total,'currency'=>'INR','status'=>'pending','payload'=>$data]);
        return $data;
    }

    public function verifyWebhook(array $payload, string $signature): bool
    {
        $secret = config('services.razorpay.webhook_secret') ?? config('services.razorpay.secret');
        if (!$secret) return false;
        $expected = hash_hmac('sha256', json_encode($payload), $secret);
        return hash_equals($expected, $signature);
    }

    public function capture(string $paymentId, int $amount): array
    {
        $key = config('services.razorpay.key'); $secret = config('services.razorpay.secret');
        $resp = Http::withBasicAuth($key, $secret)->post("https://api.razorpay.com/v1/payments/{$paymentId}/capture", ['amount'=>$amount,'currency'=>'INR']);
        return $resp->json() ?? [];
    }

    public function refund(Order $order, ?int $amount = null): array
    {
        $key = config('services.razorpay.key'); $secret = config('services.razorpay.secret');
        $pay = $order->payment; if(!$pay || !$pay->provider_payment_id) throw new \RuntimeException('No payment to refund');
        $resp = Http::withBasicAuth($key, $secret)->post('https://api.razorpay.com/v1/payments/'.$pay->provider_payment_id.'/refund', ['amount'=>$amount ?? $order->total]);
        return $resp->json() ?? [];
    }
}