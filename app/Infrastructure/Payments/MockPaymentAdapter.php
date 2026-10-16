<?php

namespace App\Infrastructure\Payments;

use App\Domain\DTOs\PaymentIntentDTO;
use App\Domain\DTOs\PaymentRedirectDTO;
use App\Domain\DTOs\PaymentResultDTO;
use App\Domain\DTOs\WebhookResultDTO;
use App\Domain\Interfaces\PaymentGatewayInterface;

class MockPaymentAdapter implements PaymentGatewayInterface
{
    public function getGatewayCode(): string
    {
        return 'mock';
    }

    public function supportsCurrency(string $currency): bool
    {
        return true; // Supports any currency for testing
    }

    public function initializePayment(PaymentIntentDTO $intent): PaymentRedirectDTO
    {
        $authUrl = "/payments/mock-checkout?reference={$intent->reference}&amount={$intent->amount}&currency={$intent->currency}";

        return new PaymentRedirectDTO(
            reference: $intent->reference,
            authorizationUrl: $authUrl,
            accessCode: 'MOCK_ACCESS_' . substr(md5($intent->reference), 0, 12),
            gateway: 'mock',
            gatewayResponse: [
                'status' => 'success',
                'message' => 'Mock payment authorization generated successfully.',
            ]
        );
    }

    public function verifyPayment(string $reference): PaymentResultDTO
    {
        return new PaymentResultDTO(
            success: true,
            reference: $reference,
            amount: 10.00, // Will be cross-checked with Payment record amount in PaymentService
            currency: 'USD',
            gatewayReference: 'MOCK_GTW_' . strtoupper(substr(md5($reference), 0, 10)),
            message: 'Mock payment verified successfully.',
            paidAt: now()->toISOString(),
            rawResponse: ['status' => 'success', 'verified' => true]
        );
    }

    public function handleWebhook(array $payload, array $headers = []): WebhookResultDTO
    {
        $reference = $payload['data']['reference'] ?? ($payload['reference'] ?? 'MOCK_REF');
        $amount = (float) ($payload['data']['amount'] ?? ($payload['amount'] ?? 10.00));
        $currency = $payload['data']['currency'] ?? ($payload['currency'] ?? 'USD');

        return new WebhookResultDTO(
            success: true,
            eventType: 'charge.success',
            reference: $reference,
            amount: $amount,
            currency: $currency,
            gatewayReference: 'MOCK_WEBHOOK_' . time(),
            message: 'Mock webhook processed.',
            data: $payload
        );
    }
}
