<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    use HasFactory;

    protected $table = 'webhook_deliveries';

    protected $fillable = ['webhook_endpoint_id', 'event', 'payload', 'response_status', 'response_body', 'attempt_count', 'status', 'delivered_at'];

    protected $casts = [
        'payload' => 'array',
        'attempt_count' => 'integer',
        'delivered_at' => 'datetime',
    ];

    public function endpoint() { return $this->belongsTo(WebhookEndpoint::class, "webhook_endpoint_id"); }
}
