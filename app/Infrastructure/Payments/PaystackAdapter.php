<?php

namespace App\Infrastructure\Payments;

use App\Domain\DTOs\PaymentIntentDTO;
use App\Domain\DTOs\PaymentRedirectDTO;
use App\Domain\DTOs\PaymentResultDTO;
use App\Domain\DTOs\WebhookResultDTO;
use App\Domain\Interfaces\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaystackAdapter implements PaymentGatewayInterface
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;

    public function __construct(
        ?string $secretKey = null,
        ?string $publicKey = null,
        ?string $baseUrl = null
    ) {
        $this->secretKey = $secretKey ?? config('services.paystack.secret_key', '');
        $this->publicKey = $publicKey ?? config('services.paystack.public_key', '');
        $this->baseUrl = rtrim($baseUrl ?? config('services.paystack.payment_url', 'https://api.paystack.co'), '/');
    }

    public function getGatewayCode(): string
    {
        return 'paystack';
    }

    public function supportsCurrency(string $currency): bool
    {
        return true;
    }

    public function initializePayment(PaymentIntentDTO $intent): PaymentRedirectDTO
    {
        if (empty($this->secretKey)) {
            throw new RuntimeException("Paystack secret key is not configured.");
        }

        $callbackUrl = $intent->callbackUrl ?? (function_exists('route') ? route('wallet.deposit.callback') : url('/wallet/callback'));
        $amountInKobo = (int) round($intent->amount * 100);

        $payload = [
            'email' => $intent->userEmail,
            'amount' => $amountInKobo,
            'currency' => strtoupper($intent->currency),
            'reference' => $intent->reference,
            'callback_url' => $callbackUrl,
            'metadata' => array_merge($intent->metadata, [
                'user_name' => $intent->userName,
                'custom_fields' => [
                    [
                        'display_name' => 'Customer Name',
                        'variable_name' => 'customer_name',
                        'value' => $intent->userName ?? $intent->userEmail,
                    ],
                ],
            ]),
        ];

        $response = Http::withToken($this->secretKey)
            ->timeout(15)
            ->post("{$this->baseUrl}/transaction/initialize", $payload);

        if (!$response->successful() || !$response->json('status')) {
            $msg = $response->json('message') ?? 'Paystack initialization failed.';
            Log::error("Paystack initialize error: {$msg}", [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);
            throw new RuntimeException("Paystack error: {$msg}");
        }

        $data = $response->json('data', []);

        return new PaymentRedirectDTO(
            reference: $data['reference'] ?? $intent->reference,
            authorizationUrl: $data['authorization_url'] ?? '',
            accessCode: $data['access_code'] ?? null,
            gateway: 'paystack',
            gatewayResponse: array_merge($data, [
                'public_key' => $this->publicKey,
            ])
        );
    }

    public function verifyPayment(string $reference): PaymentResultDTO
    {
        if (empty($this->secretKey)) {
            throw new RuntimeException("Paystack secret key is not configured.");
        }

        $response = Http::withToken($this->secretKey)
            ->timeout(15)
            ->get("{$this->baseUrl}/transaction/verify/" . rawurlencode($reference));

        if (!$response->successful() || !$response->json('status')) {
            $msg = $response->json('message') ?? 'Could not verify Paystack payment.';
            return new PaymentResultDTO(
                success: false,
                reference: $reference,
                amount: 0,
                currency: 'NGN',
                message: $msg,
                rawResponse: $response->json() ?? []
            );
        }

        $data = $response->json('data', []);
        $status = strtolower($data['status'] ?? '');
        $isSuccess = ($status === 'success');

        return new PaymentResultDTO(
            success: $isSuccess,
            reference: $data['reference'] ?? $reference,
            amount: (float) (($data['amount'] ?? 0) / 100),
            currency: $data['currency'] ?? 'NGN',
            paidAt: $data['paid_at'] ?? null,
            gatewayReference: (string) ($data['id'] ?? ''),
            rawResponse: $data,
            message: $isSuccess ? 'Payment verified successfully.' : "Payment status is {$status}."
        );
    }

    public function handleWebhook(array $payload, array $headers = []): WebhookResultDTO
    {
        $signature = $headers['x-paystack-signature'][0]
            ?? $headers['X-Paystack-Signature'][0]
            ?? $headers['x-paystack-signature']
            ?? $headers['X-Paystack-Signature']
            ?? null;

        // Verify webhook signature if present and secret key is set
        if ($signature && !empty($this->secretKey)) {
            $expectedSignature = hash_hmac('sha512', json_encode($payload), $this->secretKey);
            if (!hash_equals($expectedSignature, (string) $signature)) {
                Log::warning('Paystack webhook signature mismatch.');
                return new WebhookResultDTO(
                    success: false,
                    reference: '',
                    amount: 0,
                    currency: 'NGN',
                    event: 'invalid_signature',
                    message: 'Invalid Paystack webhook signature.'
                );
            }
        }

        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        if ($event === 'charge.success') {
            $reference = $data['reference'] ?? '';
            $amount = (float) (($data['amount'] ?? 0) / 100);
            $currency = $data['currency'] ?? 'NGN';

            return new WebhookResultDTO(
                success: true,
                reference: $reference,
                amount: $amount,
                currency: $currency,
                event: $event,
                gatewayReference: (string) ($data['id'] ?? ''),
                data: $data,
                message: 'Paystack charge.success webhook processed.'
            );
        }

        return new WebhookResultDTO(
            success: false,
            reference: $data['reference'] ?? '',
            amount: 0,
            currency: 'NGN',
            event: $event,
            message: "Ignored event: {$event}"
        );
    }
}
