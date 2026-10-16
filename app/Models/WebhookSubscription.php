<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookSubscription extends Model
{
    use HasFactory;

    protected $table = 'webhook_subscriptions';

    protected $fillable = ['webhook_endpoint_id', 'event_type'];

    public function endpoint() { return $this->belongsTo(WebhookEndpoint::class, "webhook_endpoint_id"); }
}
