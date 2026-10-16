<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRateLimit extends Model
{
    use HasFactory;

    protected $table = 'api_rate_limits';

    protected $fillable = ['api_key_id', 'endpoint', 'requests_per_minute', 'burst_limit'];

    protected $casts = [
        'requests_per_minute' => 'integer',
        'burst_limit' => 'integer',
    ];

    public function apiKey() { return $this->belongsTo(ApiKey::class); }
}
