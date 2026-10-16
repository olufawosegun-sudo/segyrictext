<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => (float) $this->balance,
            'available_balance' => (float) $this->available_balance,
            'active_holds' => (float) $this->active_holds_total,
            'currency' => [
                'id' => $this->currency?->id,
                'code' => $this->currency?->code ?? 'USD',
                'symbol' => $this->currency?->symbol ?? '$',
            ],
            'status' => $this->status,
        ];
    }
}
