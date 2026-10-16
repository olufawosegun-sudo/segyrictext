<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberReservation extends Model
{
    use HasFactory;

    protected $table = 'number_reservations';

    protected $fillable = [
        'phone_number_id',
        'user_id',
        'order_item_id',
        'status',
        'reserved_at',
        'expires_at',
        'released_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(PhoneNumber::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'reserved')
                     ->where('expires_at', '>', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'reserved')
                     ->where('expires_at', '<=', now());
    }
}

