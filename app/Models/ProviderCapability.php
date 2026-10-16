<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderCapability extends Model
{
    use HasFactory;

    protected $table = 'provider_capabilities';

    protected $fillable = ['provider_id', 'capability', 'is_supported'];

    protected $casts = [
        'is_supported' => 'boolean',
    ];

    public function provider() { return $this->belongsTo(Provider::class); }
}
