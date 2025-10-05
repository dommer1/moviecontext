<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScrapedArticle extends Model
{
    protected $fillable = [
        'title',
        'content_summary',
        'author_name',
        'published_at',
        'image_url',
        'original_url',
        'html_snapshot',
        'status',
        'source_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function article(): HasOne
    {
        return $this->hasOne(Article::class);
    }
}
