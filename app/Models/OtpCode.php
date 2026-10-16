<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $fillable = [
        'verification_session_id', 'code', 'status',
        'received_at', 'expires_at', 'used_at'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function verificationSession(): BelongsTo
    {
        return $this->belongsTo(VerificationSession::class);
    }
}
