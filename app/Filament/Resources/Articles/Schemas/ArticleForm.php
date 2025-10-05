<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                \Filament\Forms\Components\RichEditor::make('content')
                    ->required(),
                \Filament\Forms\Components\Textarea::make('excerpt')
                    ->required()
                    ->maxLength(500),
                \Filament\Forms\Components\FileUpload::make('featured_image_path')
                    ->image()
                    ->directory('articles'),
                \Filament\Forms\Components\TextInput::make('seo_title')
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('seo_description')
                    ->maxLength(500),
                \Filament\Forms\Components\DateTimePicker::make('published_at'),
                \Filament\Forms\Components\TextInput::make('view_count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                \Filament\Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('scraped_article_id')
                    ->relationship('scrapedArticle', 'title')
                    ->searchable(),
                \Filament\Forms\Components\TagsInput::make('tags')
                    ->relationship('tags', 'name')
                    ->placeholder('Vyberte tagy...'),
            ]);
    }
}
