<?php

namespace App\Infrastructure\Payments;

use App\Domain\Interfaces\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Resolve a payment gateway adapter by code.
     */
    public function make(?string $gateway = null): PaymentGatewayInterface
    {
        $code = strtolower(trim((string) ($gateway ?: config('services.default_payment_gateway', 'paystack'))));

        return match ($code) {
            'paystack' => new PaystackAdapter(),
            'flutterwave' => new FlutterwaveAdapter(),
            'mock', 'test', 'sandbox' => new MockPaymentAdapter(),
            default => new PaystackAdapter(),
        };
    }

    /**
     * Suggest the most suitable payment gateway code for a given ISO currency.
     */
    public function suggestGatewayForCurrency(string $currency): string
    {
        // Paystack is great for NGN, but also supports other currencies
        return 'paystack';
    }

    /**
     * Get the frontend-safe public key for a payment gateway's JS SDK.
     */
    public function getPublicKey(string $gateway): ?string
    {
        return match (strtolower($gateway)) {
            'paystack' => config('services.paystack.public_key') ?: null,
            'flutterwave' => config('services.flutterwave.public_key') ?: null,
            default => config('services.paystack.public_key') ?: null,
        };
    }

    /**
     * Get supported payment channels/methods for a gateway and currency.
     */
    public function getSupportedChannels(string $gateway, string $currency = 'NGN'): array
    {
        return ['card', 'apple_pay', 'google_pay'];
    }

    /**
     * Get list of all supported payment gateways and their supported features.
     */
    public function getAvailableGateways(): array
    {
        $hasPaystack = !empty(config('services.paystack.secret_key'));
        $hasFlutterwave = !empty(config('services.flutterwave.secret_key'));

        return [
            [
                'code' => 'paystack',
                'name' => 'Paystack',
                'currencies' => ['NGN', 'USD', 'GHS', 'KES', 'ZAR'],
                'is_active' => $hasPaystack,
                'has_keys' => $hasPaystack,
                'icon' => 'credit-card',
                'badge' => 'Primary Gateway',
                'description' => 'Cards, Bank Transfer, USSD & Mobile Money',
            ],
            [
                'code' => 'flutterwave',
                'name' => 'Flutterwave',
                'currencies' => ['NGN', 'USD', 'GHS', 'KES', 'EUR', 'GBP'],
                'is_active' => $hasFlutterwave,
                'has_keys' => $hasFlutterwave,
                'icon' => 'globe',
                'badge' => 'Alternative',
                'description' => 'Cards, Mobile Money, Bank Transfer & Barter',
            ],
        ];
    }
}
