<?php

namespace App\Domain\Verification\Events;

use App\Models\OtpCode;
use App\Models\VerificationSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationSessionCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public VerificationSession $session,
        public OtpCode $otpCode
    ) {}
}
