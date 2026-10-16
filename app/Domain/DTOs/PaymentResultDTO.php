<?php

namespace App\Domain\DTOs;

readonly class PaymentResultDTO
{
    public function __construct(
        public bool $success,
        public string $reference,
        public float $amount,
        public string $currency,
        public ?string $gatewayReference = null,
        public ?string $message = null,
        public ?string $paidAt = null,
        public array $rawResponse = []
    ) {}
}
