<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source', 'event_key', 'provider_id', 'event_id', 'event_type',
        'payload', 'status', 'processed_at', 'created_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
