<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NumberOrder extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'number_orders';

    protected $fillable = [
        'id',
        'user_id',
        'idempotency_key',
        'country',
        'duration_days',
        'usd_price',
        'fx_rate',
        'margin_pct',
        'price_kobo',
        'status',
        'provider_request_id',
        'provider_status',
        'phone_number',
        'expires_at',
        'failure_reason',
    ];

    protected $casts = [
        'usd_price' => 'float',
        'fx_rate' => 'float',
        'margin_pct' => 'float',
        'price_kobo' => 'integer',
        'duration_days' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'order_id')->latest('received_at');
    }

    /**
     * Map internal granular state to public consumer API state.
     * Public API collapses internal states to: processing | active | failed | expired
     * and NEVER leaks provider details or internal reasons.
     */
    public function publicStatus(): string
    {
        return match ($this->status) {
            'active' => 'active',
            'failed' => 'failed',
            'expired' => 'expired',
            default => 'processing', // created, placing, provider_pending, needs_review
        };
    }

    /**
     * Serialize for public mobile webview API.
     */
    public function toPublicArray(bool $includeSms = false): array
    {
        $data = [
            'id' => $this->id,
            'status' => $this->publicStatus(),
            'country' => $this->country,
            'duration_days' => $this->duration_days,
            'phone_number' => $this->phone_number,
            'price_ngn' => round($this->price_kobo / 100, 2),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];

        if ($includeSms) {
            $data['sms'] = $this->smsMessages()
                ->limit(20)
                ->get()
                ->map(fn($msg) => [
                    'id' => $msg->id,
                    'body' => $msg->body ?? $msg->message,
                    'from' => $msg->from_number,
                    'received_at' => $msg->received_at?->toIso8601String() ?? $msg->created_at?->toIso8601String(),
                ])
                ->all();
        }

        return $data;
    }
}
