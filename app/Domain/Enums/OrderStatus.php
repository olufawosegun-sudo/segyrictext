<?php

namespace App\Domain\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Refunded = 'refunded';
}
