<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageRecord extends Model
{
    use HasFactory;

    protected $table = 'usage_records';

    protected $fillable = ['user_id', 'metric_name', 'quantity', 'unit_cost', 'total_cost', 'record_date'];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'record_date' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
