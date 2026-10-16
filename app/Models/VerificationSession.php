<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VerificationSession extends Model
{
    protected $fillable = [
        'user_id', 'order_item_id', 'phone_number_id',
        'service_id', 'provider_id', 'status', 'verification_code',
        'expires_at', 'completed_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(PhoneNumber::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    public function latestOtp(): HasOne
    {
        return $this->hasOne(OtpCode::class)->latestOfMany();
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }
}
