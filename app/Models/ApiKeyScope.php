<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKeyScope extends Model
{
    use HasFactory;

    protected $table = 'api_key_scopes';

    protected $fillable = ['api_key_id', 'api_scope_id'];

    public function apiKey() { return $this->belongsTo(ApiKey::class); }

    public function apiScope() { return $this->belongsTo(ApiScope::class); }
}
