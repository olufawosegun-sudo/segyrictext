<?php

namespace App\Models;

use App\Domain\Enums\TransactionStatus;
use App\Domain\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'wallet_id', 'user_id', 'type', 'amount', 'amount_kobo',
        'balance_before', 'balance_after', 'balance_after_kobo', 'reference',
        'source_type', 'source_id', 'description', 'meta', 'created_at'
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:8',
        'amount_kobo' => 'integer',
        'balance_before' => 'decimal:8',
        'balance_after' => 'decimal:8',
        'balance_after_kobo' => 'integer',
        'meta' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transaction) {
            if (empty($transaction->reference)) {
                $transaction->reference = 'TXN_' . Str::ulid();
            }
        });
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
