<?php

namespace App\Domain\Wallet\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(
        public readonly float $availableBalance,
        public readonly float $requestedAmount,
        ?string $message = null
    ) {
        $message = $message ?? "Insufficient wallet funds. Requested: {$requestedAmount}, Available: {$availableBalance}";
        parent::__construct($message);
    }
}
