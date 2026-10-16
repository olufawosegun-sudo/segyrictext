<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFailure extends Model
{
    use HasFactory;

    protected $table = 'job_failures';

    protected $fillable = ['job_name', 'queue', 'payload', 'exception', 'failed_at'];

    protected $casts = [
        'payload' => 'array',
        'failed_at' => 'datetime',
    ];
}
