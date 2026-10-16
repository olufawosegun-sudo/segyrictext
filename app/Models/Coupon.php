<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = ['code', 'discount_type', 'discount_value', 'min_spend', 'max_discount', 'usage_limit', 'used_count', 'starts_at', 'expires_at', 'status'];

    protected $casts = [
        'discount_value' => 'decimal:4',
        'min_spend' => 'decimal:4',
        'max_discount' => 'decimal:4',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function usages() { return $this->hasMany(CouponUsage::class); }
}
