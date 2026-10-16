<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $table = 'exchange_rates';

    protected $fillable = ['base_currency_id', 'target_currency_id', 'rate', 'source', 'valid_from', 'valid_until'];

    protected $casts = [
        'rate' => 'decimal:8',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function baseCurrency() { return $this->belongsTo(Currency::class, "base_currency_id"); }

    public function targetCurrency() { return $this->belongsTo(Currency::class, "target_currency_id"); }
}
