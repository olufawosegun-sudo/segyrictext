<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'amount', 'currency_code',
        'provider', 'channel', 'bank_name', 'account_number',
        'account_name', 'provider_reference', 'status',
        'idempotency_key', 'paid_at', 'expires_at', 'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
            ($this->status === 'pending' && $this->expires_at && now()->isAfter($this->expires_at));
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            if (empty($payment->idempotency_key)) {
                $payment->idempotency_key = 'PAY_' . Str::ulid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
