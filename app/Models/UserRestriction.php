<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRestriction extends Model
{
    use HasFactory;

    protected $table = 'user_restrictions';

    protected $fillable = ['user_id', 'restriction_type', 'reason', 'expires_at', 'created_by'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
}
