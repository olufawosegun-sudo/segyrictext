<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'balance',
        'balance_kobo',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
        'balance_kobo' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function holds()
    {
        return $this->hasMany(WalletHold::class);
    }

    public function getActiveHoldsTotalAttribute(): float
    {
        return (float) $this->holds()->active()->sum('amount');
    }

    public function getAvailableBalanceAttribute(): float
    {
        return max(0, (float) $this->balance - $this->active_holds_total);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

