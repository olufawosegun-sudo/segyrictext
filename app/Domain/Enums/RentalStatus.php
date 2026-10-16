<?php

namespace App\Domain\Enums;

enum RentalStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Released = 'released';
}
