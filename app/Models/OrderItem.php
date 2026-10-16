<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'phone_number_id',
        'service_id',
        'country_id',
        'number_type_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:8',
        'total_price' => 'decimal:8',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(PhoneNumber::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function numberType(): BelongsTo
    {
        return $this->belongsTo(NumberType::class);
    }

    public function verificationSession(): HasOne
    {
        return $this->hasOne(VerificationSession::class);
    }
}
