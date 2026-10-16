<?php

namespace App\Domain\Wallet\Events;

use App\Models\Wallet;
use App\Models\WalletHold;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletHoldCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Wallet $wallet,
        public readonly WalletHold $hold
    ) {}
}
