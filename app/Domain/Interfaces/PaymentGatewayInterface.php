<?php

namespace App\Domain\Interfaces;

use App\Domain\DTOs\PaymentIntentDTO;
use App\Domain\DTOs\PaymentRedirectDTO;
use App\Domain\DTOs\PaymentResultDTO;
use App\Domain\DTOs\WebhookResultDTO;

interface PaymentGatewayInterface
{
    /**
     * Get the unique gateway identifier code (e.g. 'stripe', 'flutterwave', 'mock').
     */
    public function getGatewayCode(): string;

    /**
     * Check if a given ISO currency code is supported by this gateway.
     */
    public function supportsCurrency(string $currency): bool;

    /**
     * Initialize a payment transaction and generate checkout redirect details.
     */
    public function initializePayment(PaymentIntentDTO $intent): PaymentRedirectDTO;

    /**
     * Verify a transaction using gateway reference or internal payment reference.
     */
    public function verifyPayment(string $reference): PaymentResultDTO;

    /**
     * Handle incoming asynchronous webhook payload from the payment provider.
     */
    public function handleWebhook(array $payload, array $headers = []): WebhookResultDTO;
}
