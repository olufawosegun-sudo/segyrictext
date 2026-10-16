<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = ['code', 'name', 'symbol', 'decimal_places', 'is_default', 'status'];

    protected $casts = [
        'is_default' => 'boolean',
        'decimal_places' => 'integer',
    ];

    public function baseExchangeRates() { return $this->hasMany(ExchangeRate::class, "base_currency_id"); }

    public function targetExchangeRates() { return $this->hasMany(ExchangeRate::class, "target_currency_id"); }

    public function wallets() { return $this->hasMany(Wallet::class); }
}
