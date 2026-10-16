<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerificationSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $remainingSeconds = max(0, now()->diffInSeconds($this->expires_at, false));

        return [
            'id' => $this->id,
            'status' => $this->status,
            'phone_number' => [
                'id' => $this->phoneNumber?->id,
                'e164' => $this->phoneNumber?->phone_number,
                'country' => $this->phoneNumber?->country?->name,
                'country_code' => $this->phoneNumber?->country?->iso2,
                'dial_code' => $this->phoneNumber?->country?->dial_code,
            ],
            'service' => [
                'id' => $this->service?->id,
                'name' => $this->service?->name,
                'code' => $this->service?->code,
            ],
            'price' => (float) ($this->orderItem?->unit_price ?? 0.0),
            'currency' => $this->orderItem?->order?->currency_code ?? 'USD',
            'verification_code' => $this->verification_code,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'remaining_seconds' => $remainingSeconds,
            'created_at' => $this->created_at?->toIso8601String(),
            'messages' => $this->smsMessages->map(fn($msg) => [
                'id' => $msg->id,
                'from' => $msg->from_number,
                'message' => $msg->message,
                'received_at' => $msg->received_at?->toIso8601String(),
            ]),
        ];
    }
}
