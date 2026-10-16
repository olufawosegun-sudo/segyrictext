<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name', 'code', 'description', 'category', 'is_active', 'is_best_value', 'last_synced_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_best_value' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_services')
            ->withPivot(['status', 'price', 'currency', 'minimum_price', 'maximum_price'])
            ->withTimestamps();
    }

    public function countryServices(): HasMany
    {
        return $this->hasMany(CountryService::class);
    }

    public function phoneNumbers(): BelongsToMany
    {
        return $this->belongsToMany(PhoneNumber::class, 'phone_number_services')
            ->withPivot('status')
            ->withTimestamps();
    }
}
