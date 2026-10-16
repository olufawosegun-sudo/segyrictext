<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialReconciliation extends Model
{
    use HasFactory;

    protected $table = 'financial_reconciliations';

    protected $fillable = ['period_start', 'period_end', 'total_topups', 'total_spent', 'total_refunds', 'closing_balance', 'status', 'notes'];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_topups' => 'decimal:4',
        'total_spent' => 'decimal:4',
        'total_refunds' => 'decimal:4',
        'closing_balance' => 'decimal:4',
    ];
}
