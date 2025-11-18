<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;

class CashPaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(array $data): array
    {
        // Cash on delivery - no payment intent needed
        return [
            'success' => true,
            'payment_id' => 'cash_' . uniqid(),
            'status' => 'pending',
            'message' => 'Cash on delivery selected',
        ];
    }

    public function capturePayment(string $paymentId, array $data = []): array
    {
        // Mark as completed when cash is received
        return [
            'success' => true,
            'payment_id' => $paymentId,
            'status' => 'completed',
            'amount' => $data['amount'] ?? 0,
        ];
    }

    public function refundPayment(string $paymentId, float $amount): array
    {
        // Cash refunds handled manually
        return [
            'success' => true,
            'refund_id' => 'refund_' . uniqid(),
            'status' => 'pending_manual',
            'amount' => $amount,
        ];
    }

    public function getPaymentStatus(string $paymentId): array
    {
        return [
            'payment_id' => $paymentId,
            'status' => 'pending',
        ];
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        // No webhooks for cash payments
        return true;
    }

    public function processWebhook(array $payload): array
    {
        // No webhooks for cash payments
        return ['event_type' => 'none'];
    }

    public function getName(): string
    {
        return 'cash';
    }
}
