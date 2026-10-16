<?php

namespace App\Domain\Wallet\Exceptions;

use Exception;

class InvalidHoldStateException extends Exception
{
    public function __construct(
        public readonly int $holdId,
        public readonly string $currentStatus,
        public readonly string $attemptedAction,
        ?string $message = null
    ) {
        $message = $message ?? "Cannot {$attemptedAction} wallet hold #{$holdId} because it is in '{$currentStatus}' state.";
        parent::__construct($message);
    }
}
