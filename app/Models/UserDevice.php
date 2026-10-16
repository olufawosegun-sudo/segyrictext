<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $table = 'user_devices';

    protected $fillable = ['user_id', 'device_fingerprint', 'platform', 'browser', 'last_ip', 'is_trusted', 'last_seen_at'];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
