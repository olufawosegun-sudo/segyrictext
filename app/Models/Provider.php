<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'status', 'priority'
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(ProviderAccount::class);
    }

    public function providerCountries(): HasMany
    {
        return $this->hasMany(ProviderCountry::class);
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(PhoneNumber::class);
    }

    public function healthChecks(): HasMany
    {
        return $this->hasMany(ProviderHealthCheck::class);
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(WebhookEvent::class);
    }
}
