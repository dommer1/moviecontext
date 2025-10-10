<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image_path',
        'seo_title',
        'seo_description',
        'published_at',
        'view_count',
        'author_id',
        'scraped_article_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function scrapedArticle(): BelongsTo
    {
        return $this->belongsTo(ScrapedArticle::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function metadata(): HasOne
    {
        return $this->hasOne(ArticleMetadata::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));

        return ceil($words / 200); // Assuming 200 words per minute
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image_path) {
            return null;
        }

        return asset('storage/'.$this->featured_image_path);
    }
}
