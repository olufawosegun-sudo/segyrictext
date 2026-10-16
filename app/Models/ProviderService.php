<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    use HasFactory;

    protected $table = 'provider_services';

    protected $fillable = ['provider_id', 'service_id', 'provider_service_code', 'cost_price', 'status'];

    protected $casts = [
        'cost_price' => 'decimal:4',
    ];

    public function provider() { return $this->belongsTo(Provider::class); }

    public function service() { return $this->belongsTo(Service::class); }
}
