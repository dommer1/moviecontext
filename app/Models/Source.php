<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    protected $fillable = [
        'name',
        'url',
        'type',
        'language',
        'active',
        'last_checked_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function scrapedArticles(): HasMany
    {
        return $this->hasMany(ScrapedArticle::class);
    }
}
