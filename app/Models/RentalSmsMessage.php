<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalSmsMessage extends Model
{
    use HasFactory;

    protected $table = 'rental_sms_messages';

    protected $fillable = ['number_rental_id', 'sender', 'recipient', 'message', 'received_at'];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function numberRental() { return $this->belongsTo(NumberRental::class); }
}
