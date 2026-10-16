<?php

namespace App\Domain\Verification\Events;

use App\Models\SmsMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmsReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SmsMessage $smsMessage) {}
}
