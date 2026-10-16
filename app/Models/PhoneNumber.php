<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneNumber extends Model
{
    protected $fillable = [
        'provider_id', 'country_id', 'number_type_id',
        'phone_number', 'provider_number_id', 'status',
        'capabilities', 'last_synced_at'
    ];

    protected $casts = [
        'capabilities' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function numberType(): BelongsTo
    {
        return $this->belongsTo(NumberType::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'phone_number_services')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function verificationSessions(): HasMany
    {
        return $this->hasMany(VerificationSession::class);
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }
}
