<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskRule extends Model
{
    use HasFactory;

    protected $table = 'risk_rules';

    protected $fillable = ['name', 'condition_type', 'parameters', 'action', 'status'];

    protected $casts = [
        'parameters' => 'array',
    ];
}
