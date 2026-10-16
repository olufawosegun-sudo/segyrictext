<?php

namespace App\Domain\Number\Exceptions;

use Exception;

class NumberUnavailableException extends Exception
{
    public function __construct(
        public readonly int|string $phoneNumberId = 0,
        public readonly string $currentStatus = 'unavailable',
        ?string $message = null
    ) {
        if (is_string($phoneNumberId) && !is_numeric($phoneNumberId) && $message === null) {
            $msg = $phoneNumberId;
        } else {
            $msg = $message ?? "Phone number #{$phoneNumberId} is currently '{$currentStatus}' and cannot be reserved.";
        }
        parent::__construct($msg);
    }
}
