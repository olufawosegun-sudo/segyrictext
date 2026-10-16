<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $table = 'user_sessions';

    protected $fillable = ['user_id', 'token_hash', 'ip_address', 'user_agent', 'last_active_at', 'expires_at'];

    protected $casts = [
        'last_active_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
