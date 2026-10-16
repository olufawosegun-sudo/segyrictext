<?php

namespace App\Domain\Enums;

enum SmsProcessingStatus: string
{
    case Received = 'received';
    case Processed = 'processed';
    case Ignored = 'ignored';
    case Duplicate = 'duplicate';
    case Invalid = 'invalid';
}
