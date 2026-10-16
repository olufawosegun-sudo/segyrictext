<?php

namespace App\Domain\Enums;

enum OrderType: string
{
    case Verification = 'verification';
    case Rental = 'rental';
    case BalanceTopup = 'balance_topup';
}
