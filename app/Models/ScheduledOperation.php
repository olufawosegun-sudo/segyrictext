<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledOperation extends Model
{
    use HasFactory;

    protected $table = 'scheduled_operations';

    protected $fillable = [
        'operation_type',
        'reference_type',
        'reference_id',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
