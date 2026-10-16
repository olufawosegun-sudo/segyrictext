<?php

namespace App\Domain\DTOs;

readonly class PaymentIntentDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $userEmail,
        public string $reference,
        public ?string $userName = null,
        public ?string $callbackUrl = null,
        public array $metadata = []
    ) {}
}
