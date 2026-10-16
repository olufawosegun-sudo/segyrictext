<?php

namespace App\Domain\Wallet\Exceptions;

use Exception;

class WalletSuspendedException extends Exception
{
    public function __construct(
        public readonly int $walletId,
        public readonly string $status,
        ?string $message = null
    ) {
        $message = $message ?? "Wallet #{$walletId} is {$status} and cannot process operations.";
        parent::__construct($message);
    }
}
