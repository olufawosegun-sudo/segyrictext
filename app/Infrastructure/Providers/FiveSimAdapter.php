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

class FiveSimAdapter implements NumberProviderInterface
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
        $apiKey = $this->account?->credentials['api_key'] ?? config('services.5sim.key');

        if ($apiKey && !app()->environment('testing')) {
            try {
                $countryCode = strtolower($country->iso2);
                $serviceCode = strtolower($service->code);

                $response = Http::withToken($apiKey)
                    ->get("https://5sim.net/v1/user/buy/activation/{$countryCode}/any/{$serviceCode}");

                if ($response->successful()) {
                    $data = $response->json();
                    return new ProviderNumberDTO(
                        providerNumberId: (string) $data['id'],
                        phoneNumber: $data['phone'],
                        costPrice: (float) ($data['price'] ?? 0.35),
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
        $providerId = '5SIM_' . mt_rand(10000000, 99999999);

        return new ProviderNumberDTO(
            providerNumberId: $providerId,
            phoneNumber: $fullNumber,
            costPrice: 0.35,
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
