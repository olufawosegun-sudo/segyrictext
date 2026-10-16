<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalRenewal extends Model
{
    protected $fillable = [
        'rental_id', 'old_expires_at', 'new_expires_at',
        'amount', 'currency_code', 'status'
    ];

    protected $casts = [
        'old_expires_at' => 'datetime',
        'new_expires_at' => 'datetime',
        'amount' => 'decimal:8',
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(NumberRental::class, 'rental_id');
    }
}
