<?php

namespace App\Domain\Enums;

enum VerificationStatus: string
{
    case Pending = 'pending';
    case Searching = 'searching';
    case Assigned = 'assigned';
    case WaitingForSms = 'waiting_for_sms';
    case SmsReceived = 'sms_received';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
