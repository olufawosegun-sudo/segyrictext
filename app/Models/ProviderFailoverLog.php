<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderFailoverLog extends Model
{
    use HasFactory;

    protected $table = 'provider_failover_logs';

    protected $fillable = ['from_provider_id', 'to_provider_id', 'service_id', 'country_id', 'reason'];

    public function fromProvider() { return $this->belongsTo(Provider::class, "from_provider_id"); }

    public function toProvider() { return $this->belongsTo(Provider::class, "to_provider_id"); }

    public function service() { return $this->belongsTo(Service::class); }

    public function country() { return $this->belongsTo(Country::class); }
}
