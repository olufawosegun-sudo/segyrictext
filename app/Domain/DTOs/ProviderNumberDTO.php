<?php

namespace App\Domain\DTOs;

readonly class ProviderNumberDTO
{
    public function __construct(
        public string $providerNumberId,
        public string $phoneNumber,
        public float $costPrice,
        public string $currency = 'USD',
        public array $capabilities = ['sms'],
        public ?int $expiresInSeconds = 1200
    ) {}
}
