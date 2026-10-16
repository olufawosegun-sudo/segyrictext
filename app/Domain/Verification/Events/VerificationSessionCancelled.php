<?php

namespace App\Domain\Verification\Events;

use App\Models\VerificationSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationSessionCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public VerificationSession $session,
        public string $reason = 'user_cancelled'
    ) {}
}
