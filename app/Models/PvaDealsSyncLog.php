<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PvaDealsSyncLog extends Model
{
    protected $table = 'pva_deals_sync_logs';

    protected $fillable = [
        'status',
        'services_count',
        'countries_count',
        'updated_count',
        'created_count',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'services_count' => 'integer',
        'countries_count' => 'integer',
        'updated_count' => 'integer',
        'created_count' => 'integer',
        'synced_at' => 'datetime',
    ];
}
