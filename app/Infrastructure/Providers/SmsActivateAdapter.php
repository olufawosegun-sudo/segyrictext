<?php

namespace App\Infrastructure\Providers;

use App\Domain\DTOs\IncomingSmsDTO;
use App\Domain\DTOs\ProviderNumberDTO;
use App\Domain\Interfaces\NumberProviderInterface;
use App\Models\Country;
use App\Models\ProviderAccount;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SmsActivateAdapter implements NumberProviderInterface
{
    public function __construct(
        protected ?ProviderAccount $account = null
    ) {}

    public function checkAvailability(Country $country, Service $service): bool
    {
        return true;
    }

    public function reserveNumber(Country $country, Service $service): ProviderNumberDTO
    {
        $apiKey = $this->account?->credentials['api_key'] ?? config('services.sms_activate.key');

        if ($apiKey && !app()->environment('testing')) {
            try {
                $response = Http::get("https://api.sms-activate.org/stubs/handler_api.php", [
                    'api_key' => $apiKey,
                    'action' => 'getNumber',
                    'service' => strtolower($service->code),
                    'country' => $country->dial_code,
                ]);

                if ($response->successful() && str_starts_with($response->body(), 'ACCESS_NUMBER')) {
                    $parts = explode(':', $response->body());
                    return new ProviderNumberDTO(
                        providerNumberId: $parts[1],
                        phoneNumber: '+' . $parts[2],
                        costPrice: 0.30,
                        currency: 'USD',
                        capabilities: ['sms', 'otp'],
                        expiresInSeconds: 1200
                    );
                }
            } catch (\Throwable) {
                // Fall back to sandbox simulation
            }
        }

        $dial = ltrim($country->dial_code, '+');
        $randomSubscriber = mt_rand(2000000000, 9999999999);
        $fullNumber = "+{$dial}" . substr((string) $randomSubscriber, 0, 10);
        $providerId = 'ACT_' . mt_rand(100000000, 999999999);

        return new ProviderNumberDTO(
            providerNumberId: $providerId,
            phoneNumber: $fullNumber,
            costPrice: 0.30,
            currency: 'USD',
            capabilities: ['sms', 'otp'],
            expiresInSeconds: 1200
        );
    }

    public function getMessages(string $providerNumberId): array
    {
        return [];
    }

    public function releaseNumber(string $providerNumberId): bool
    {
        return true;
    }
}
