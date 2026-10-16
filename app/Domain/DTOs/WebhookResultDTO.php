<?php

namespace App\Domain\DTOs;

readonly class WebhookResultDTO
{
    public function __construct(
        public bool $success,
        public string $eventType,
        public ?string $reference = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $gatewayReference = null,
        public ?string $message = null,
        public array $data = []
    ) {}
}
