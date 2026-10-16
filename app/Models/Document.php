<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'format',
        'status',
        'origin_platform',
        'tags',
        'scheduled_at',
        'analysis_metrics',
        'word_count',
        'char_count',
        'processed_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'analysis_metrics' => 'array',
        'scheduled_at' => 'datetime',
        'processed_at' => 'datetime',
        'word_count' => 'integer',
        'char_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
