<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'bio',
        'avatar_path',
        'specialization',
        'personality_prompt',
        'writing_style_prompt',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
