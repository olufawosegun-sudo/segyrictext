<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    protected $fillable = [
        'country_id', 'service_id', 'number_type_id', 'provider_id',
        'purchase_price', 'selling_price', 'currency_code',
        'priority', 'is_active', 'starts_at', 'ends_at'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:8',
        'selling_price' => 'decimal:8',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function numberType(): BelongsTo
    {
        return $this->belongsTo(NumberType::class);
    }
}
