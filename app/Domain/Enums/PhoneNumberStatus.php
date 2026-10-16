<?php

namespace App\Domain\Enums;

enum PhoneNumberStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Assigned = 'assigned';
    case Rented = 'rented';
    case Cooldown = 'cooldown';
    case Expired = 'expired';
    case Blocked = 'blocked';
    case Retired = 'retired';
}
