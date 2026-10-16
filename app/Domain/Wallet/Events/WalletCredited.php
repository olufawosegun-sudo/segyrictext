<?php

namespace App\Domain\Wallet\Events;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletCredited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Wallet $wallet,
        public readonly WalletTransaction $transaction
    ) {}
}
