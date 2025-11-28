<?php

namespace App\Filament\Resources\ScrapedArticles\Pages;

use App\Filament\Resources\ScrapedArticles\ScrapedArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScrapedArticle extends EditRecord
{
    protected static string $resource = ScrapedArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
