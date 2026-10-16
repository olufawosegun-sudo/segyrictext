<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency_code',
        'idempotency_key',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function verificationSessions()
    {
        return $this->hasMany(VerificationSession::class);
    }

    public function numberRentals()
    {
        return $this->hasMany(NumberRental::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }
}
