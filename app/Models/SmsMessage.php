<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    protected $fillable = [
        'order_id', 'phone_number_id', 'verification_session_id', 'provider_id',
        'from_number', 'message', 'body', 'provider_message_id', 'dedupe_hash', 'received_at'
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(NumberOrder::class, 'order_id');
    }

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(PhoneNumber::class);
    }

    public function verificationSession(): BelongsTo
    {
        return $this->belongsTo(VerificationSession::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
