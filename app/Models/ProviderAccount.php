<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderAccount extends Model
{
    protected $fillable = [
        'provider_id', 'name', 'api_base_url', 'credentials',
        'balance', 'currency', 'status', 'last_balance_check_at'
    ];

    protected $casts = [
        'credentials' => 'array',
        'balance' => 'decimal:8',
        'last_balance_check_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(PhoneNumber::class);
    }
}
