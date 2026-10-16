<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderCountry extends Model
{
    protected $fillable = [
        'provider_id', 'country_id', 'number_type_id',
        'cost_price', 'currency', 'availability_status'
    ];

    protected $casts = [
        'cost_price' => 'decimal:8',
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
}
