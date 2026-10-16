<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ApiScope extends Model
{
    protected $fillable = ['name', 'description'];

    public function apiKeys(): BelongsToMany
    {
        return $this->belongsToMany(ApiKey::class, 'api_key_scopes');
    }
}
