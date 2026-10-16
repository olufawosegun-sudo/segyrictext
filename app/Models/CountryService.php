<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryService extends Model
{
    protected $fillable = [
        'country_id', 'service_id', 'pva_service_id', 'is_active',
        'supplier_str_price', 'supplier_ltr3_price', 'supplier_ltr7_price',
        'supplier_ltr14_price', 'supplier_ltr30_price',
        'supplier_ltr90_price', 'supplier_ltr180_price',
        'customer_str_price_kobo', 'str_stock', 'ltr_stock',
        'is_best_value', 'last_synced_at', 'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_best_value' => 'boolean',
        'supplier_str_price' => 'float',
        'supplier_ltr3_price' => 'float',
        'supplier_ltr7_price' => 'float',
        'supplier_ltr14_price' => 'float',
        'supplier_ltr30_price' => 'float',
        'supplier_ltr90_price' => 'float',
        'supplier_ltr180_price' => 'float',
        'customer_str_price_kobo' => 'integer',
        'str_stock' => 'integer',
        'ltr_stock' => 'integer',
        'last_synced_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
