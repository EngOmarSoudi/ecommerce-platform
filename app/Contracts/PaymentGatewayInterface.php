<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create a payment intent/session
     */
    public function createPayment(array $data): array;

    /**
     * Capture/confirm a payment
     */
    public function capturePayment(string $paymentId, array $data = []): array;

    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentId, float $amount): array;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): array;

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool;

    /**
     * Process webhook event
     */
    public function processWebhook(array $payload): array;

    /**
     * Get gateway name
     */
    public function getName(): string;
}
