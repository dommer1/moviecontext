<?php

namespace App\Filament\Resources\ScrapedArticles;

use App\Filament\Resources\ScrapedArticles\Pages\CreateScrapedArticle;
use App\Filament\Resources\ScrapedArticles\Pages\EditScrapedArticle;
use App\Filament\Resources\ScrapedArticles\Pages\ListScrapedArticles;
use App\Filament\Resources\ScrapedArticles\Schemas\ScrapedArticleForm;
use App\Filament\Resources\ScrapedArticles\Tables\ScrapedArticlesTable;
use App\Models\ScrapedArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScrapedArticleResource extends Resource
{
    protected static ?string $model = ScrapedArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ScrapedArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScrapedArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScrapedArticles::route('/'),
            'create' => CreateScrapedArticle::route('/create'),
            'edit' => EditScrapedArticle::route('/{record}/edit'),
        ];
    }
}
