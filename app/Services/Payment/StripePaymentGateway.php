<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Webhook;

class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPayment(array $data): array
    {
        $paymentIntent = PaymentIntent::create([
            'amount' => (int)($data['amount'] * 100), // Convert to cents
            'currency' => $data['currency'] ?? 'usd',
            'metadata' => $data['metadata'] ?? [],
            'description' => $data['description'] ?? null,
            'receipt_email' => $data['email'] ?? null,
        ]);

        return [
            'success' => true,
            'payment_id' => $paymentIntent->id,
            'client_secret' => $paymentIntent->client_secret,
            'status' => $paymentIntent->status,
        ];
    }

    public function capturePayment(string $paymentId, array $data = []): array
    {
        $paymentIntent = PaymentIntent::retrieve($paymentId);
        $paymentIntent->capture();

        return [
            'success' => true,
            'payment_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount / 100,
        ];
    }

    public function refundPayment(string $paymentId, float $amount): array
    {
        $refund = Refund::create([
            'payment_intent' => $paymentId,
            'amount' => (int)($amount * 100),
        ]);

        return [
            'success' => true,
            'refund_id' => $refund->id,
            'status' => $refund->status,
            'amount' => $refund->amount / 100,
        ];
    }

    public function getPaymentStatus(string $paymentId): array
    {
        $paymentIntent = PaymentIntent::retrieve($paymentId);

        return [
            'payment_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount / 100,
            'currency' => $paymentIntent->currency,
        ];
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        try {
            Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function processWebhook(array $payload): array
    {
        $event = $payload;
        
        return [
            'event_type' => $event['type'],
            'payment_id' => $event['data']['object']['id'] ?? null,
            'status' => $event['data']['object']['status'] ?? null,
        ];
    }

    public function getName(): string
    {
        return 'stripe';
    }
}
