<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'provider_customer_id',
        'provider_method_id',
        'last_four',
        'expiry_month',
        'expiry_year',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
