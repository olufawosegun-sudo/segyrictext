<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumberService extends Model
{
    use HasFactory;

    protected $table = 'phone_number_services';

    protected $fillable = ['phone_number_id', 'service_id', 'status'];

    public function phoneNumber() { return $this->belongsTo(PhoneNumber::class); }

    public function service() { return $this->belongsTo(Service::class); }
}
