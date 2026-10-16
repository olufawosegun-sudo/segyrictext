<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    use HasFactory;

    protected $table = 'daily_statistics';

    protected $fillable = ['date', 'new_users_count', 'active_users_count', 'orders_count', 'revenue', 'cost', 'verifications_count', 'successful_verifications_count'];

    protected $casts = [
        'date' => 'date',
        'new_users_count' => 'integer',
        'active_users_count' => 'integer',
        'orders_count' => 'integer',
        'revenue' => 'decimal:4',
        'cost' => 'decimal:4',
        'verifications_count' => 'integer',
        'successful_verifications_count' => 'integer',
    ];
}
