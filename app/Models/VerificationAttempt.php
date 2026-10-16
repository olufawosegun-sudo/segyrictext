<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationAttempt extends Model
{
    use HasFactory;

    protected $table = 'verification_attempts';

    protected $fillable = ['verification_session_id', 'provider_reference', 'attempted_at', 'status', 'raw_payload'];

    protected $casts = [
        'attempted_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function verificationSession() { return $this->belongsTo(VerificationSession::class); }
}
