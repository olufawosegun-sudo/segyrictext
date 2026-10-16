<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNote extends Model
{
    use HasFactory;

    protected $table = 'admin_notes';

    protected $fillable = ['admin_id', 'notable_type', 'notable_id', 'note'];

    public function admin() { return $this->belongsTo(User::class, "admin_id"); }

    public function notable() { return $this->morphTo(); }
}
