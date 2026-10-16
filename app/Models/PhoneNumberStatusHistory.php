<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumberStatusHistory extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'phone_number_status_history';

    protected $fillable = [
        'phone_number_id',
        'old_status',
        'new_status',
        'reason',
        'created_at',
    ];

    public function phoneNumber()
    {
        return $this->belongsTo(PhoneNumber::class);
    }
}

