<?php

namespace App\Domain\Verification\Enums;

enum VerificationStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SMS_RECEIVED = 'sms_received';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::EXPIRED, self::CANCELLED, self::FAILED]);
    }

    public function canReceiveSms(): bool
    {
        return in_array($this, [self::PENDING, self::ACTIVE, self::SMS_RECEIVED]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::ACTIVE]);
    }
}
