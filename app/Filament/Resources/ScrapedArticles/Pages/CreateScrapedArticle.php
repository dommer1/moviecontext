<?php

namespace App\Filament\Resources\ScrapedArticles\Pages;

use App\Filament\Resources\ScrapedArticles\ScrapedArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScrapedArticle extends CreateRecord
{
    protected static string $resource = ScrapedArticleResource::class;
}
