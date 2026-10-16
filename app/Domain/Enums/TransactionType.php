<?php

namespace App\Domain\Enums;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Purchase = 'purchase';
    case Refund = 'refund';
    case Fee = 'fee';
    case Withdrawal = 'withdrawal';
    case Adjustment = 'adjustment';
}
