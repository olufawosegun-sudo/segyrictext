<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderHealthCheck extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'provider_id', 'provider_account_id', 'status',
        'response_time_ms', 'error_message', 'checked_at', 'created_at'
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(ProviderAccount::class);
    }
}
