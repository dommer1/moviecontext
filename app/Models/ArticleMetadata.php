<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleMetadata extends Model
{
    protected $fillable = [
        'article_id',
        'imdb_id',
        'csfd_url',
        'czech_release_date',
        'streaming_platforms',
        'fun_fact',
    ];

    protected $casts = [
        'czech_release_date' => 'date',
        'streaming_platforms' => 'array',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
