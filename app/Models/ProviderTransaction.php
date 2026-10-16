<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderTransaction extends Model
{
    use HasFactory;

    protected $table = 'provider_transactions';

    protected $fillable = ['provider_id', 'provider_account_id', 'transaction_type', 'amount', 'currency', 'external_reference', 'status'];

    protected $casts = [
        'amount' => 'decimal:4',
    ];

    public function provider() { return $this->belongsTo(Provider::class); }

    public function account() { return $this->belongsTo(ProviderAccount::class, "provider_account_id"); }
}
