<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'login_attempts';

    protected $fillable = ['email', 'ip_address', 'user_agent', 'is_successful', 'attempted_at'];

    protected $casts = [
        'is_successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];
}
