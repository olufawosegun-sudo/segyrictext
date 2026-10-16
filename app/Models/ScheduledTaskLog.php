<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTaskLog extends Model
{
    use HasFactory;

    protected $table = 'scheduled_task_logs';

    protected $fillable = [
        'task_name',
        'command',
        'status',
        'output',
        'error_message',
        'duration_ms',
        'records_affected',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_ms' => 'integer',
        'records_affected' => 'integer',
    ];
}
