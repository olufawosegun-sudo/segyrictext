<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletHold extends Model
{
    use HasFactory;

    protected $table = 'wallet_holds';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'amount',
        'reference',
        'status',
        'expires_at',
        'released_at',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->whereNotNull('expires_at')
                     ->where('expires_at', '<', now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

