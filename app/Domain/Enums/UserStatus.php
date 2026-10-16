<?php

namespace App\Domain\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Blocked = 'blocked';
    case Pending = 'pending';
}
