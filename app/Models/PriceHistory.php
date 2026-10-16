<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;

    protected $table = 'price_history';

    protected $fillable = ['pricing_rule_id', 'old_price', 'new_price', 'changed_by', 'reason'];

    protected $casts = [
        'old_price' => 'decimal:4',
        'new_price' => 'decimal:4',
    ];

    public function pricingRule() { return $this->belongsTo(PricingRule::class); }

    public function changedBy() { return $this->belongsTo(User::class, "changed_by"); }
}
