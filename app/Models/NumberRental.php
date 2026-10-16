<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NumberRental extends Model
{
    protected $fillable = [
        'user_id', 'phone_number_id', 'service_id',
        'status', 'start_at', 'expires_at',
        'price', 'currency_code'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expires_at' => 'datetime',
        'price' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(PhoneNumber::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(RentalRenewal::class, 'rental_id');
    }
}
