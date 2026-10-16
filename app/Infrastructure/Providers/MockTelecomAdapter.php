<?php

namespace App\Infrastructure\Providers;

use App\Domain\DTOs\IncomingSmsDTO;
use App\Domain\DTOs\ProviderNumberDTO;
use App\Domain\Interfaces\NumberProviderInterface;
use App\Models\Country;
use App\Models\Service;
use Illuminate\Support\Str;

class MockTelecomAdapter implements NumberProviderInterface
{
    public function checkAvailability(Country $country, Service $service): bool
    {
        return true;
    }

    public function reserveNumber(Country $country, Service $service): ProviderNumberDTO
    {
        $dial = ltrim($country->dial_code, '+');
        $randomSubscriber = mt_rand(2000000000, 9999999999);
        $fullNumber = "+{$dial}" . substr((string) $randomSubscriber, 0, 10);
        $providerId = 'MOCK_' . Str::upper(Str::random(12));

        return new ProviderNumberDTO(
            providerNumberId: $providerId,
            phoneNumber: $fullNumber,
            costPrice: 0.25,
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
