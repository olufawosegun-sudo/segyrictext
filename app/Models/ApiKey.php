<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id', 'name', 'key_hash',
        'last_used_at', 'expires_at', 'revoked_at'
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(ApiScope::class, 'api_key_scopes');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ApiRequest::class);
    }
}
