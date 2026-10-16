<?php

namespace App\Domain\DTOs;

readonly class PaymentRedirectDTO
{
    public function __construct(
        public string $reference,
        public string $authorizationUrl,
        public ?string $accessCode = null,
        public string $gateway = 'mock',
        public array $gatewayResponse = []
    ) {}
}
