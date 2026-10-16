<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequest extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_key_id', 'user_id', 'method', 'endpoint',
        'status_code', 'ip_address', 'response_time_ms',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
