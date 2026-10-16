<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name', 'iso2', 'iso3', 'dial_code', 'currency_code', 'is_active', 'is_best_value', 'last_synced_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_best_value' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'country_services')
            ->withPivot(['status', 'price', 'currency', 'minimum_price', 'maximum_price'])
            ->withTimestamps();
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(PhoneNumber::class);
    }

    public function countryServices(): HasMany
    {
        return $this->hasMany(CountryService::class);
    }
}
