<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $table = 'security_events';

    protected $fillable = ['user_id', 'event_type', 'ip_address', 'user_agent', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
