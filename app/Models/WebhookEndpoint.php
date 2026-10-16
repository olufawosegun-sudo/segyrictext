<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    use HasFactory;

    protected $table = 'webhook_endpoints';

    protected $fillable = ['user_id', 'url', 'secret', 'status'];

    public function user() { return $this->belongsTo(User::class); }

    public function subscriptions() { return $this->hasMany(WebhookSubscription::class); }

    public function deliveries() { return $this->hasMany(WebhookDelivery::class); }
}
