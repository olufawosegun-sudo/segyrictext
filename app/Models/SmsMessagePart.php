<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsMessagePart extends Model
{
    use HasFactory;

    protected $table = 'sms_message_parts';

    protected $fillable = ['sms_message_id', 'part_number', 'total_parts', 'payload'];

    protected $casts = [
        'part_number' => 'integer',
        'total_parts' => 'integer',
    ];

    public function smsMessage() { return $this->belongsTo(SmsMessage::class); }
}
