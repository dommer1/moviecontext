<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(7)
            ->components([
                // Hlavná sekcia - 3/5 šírky (3 stĺpce)
                Section::make('Obsah článku')
                    ->columnSpan(5)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\RichEditor::make('content')
                            ->required(),
                    ]),

                // Sidebar sekcia - 2/5 šírky (2 stĺpce)
                Section::make('Nastavenia')
                    ->columnSpan(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('excerpt')
                            ->required()
                            ->maxLength(500)
                            ->rows(3),
                        \Filament\Forms\Components\FileUpload::make('featured_image_path')
                            ->image()
                            ->directory('articles')
                            ->imagePreviewHeight('250'),
                        \Filament\Forms\Components\Select::make('author_id')
                            ->relationship('author', 'name')
                            ->required()
                            ->searchable(),

                        // SEO nastavenia
                        \Filament\Forms\Components\TextInput::make('seo_title')
                            ->maxLength(255)
                            ->helperText('Ak je prázdne, použije sa názov článku'),
                        \Filament\Forms\Components\Textarea::make('seo_description')
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Popis pre vyhľadávače (optimálne 150-160 znakov)'),

                        // Publikovanie a štatistiky
                        \Filament\Forms\Components\DateTimePicker::make('published_at')
                            ->label('Dátum publikovania')
                            ->helperText('Ak je prázdne, článok je v koncepte'),
                        \Filament\Forms\Components\TextInput::make('view_count')
                            ->label('Počet zobrazení')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        \Filament\Forms\Components\Select::make('scraped_article_id')
                            ->relationship('scrapedArticle', 'title')
                            ->label('Zdrojový článok')
                            ->searchable()
                            ->helperText('Článok z ktorého bol generovaný'),
                        \Filament\Forms\Components\Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Vyberte tagy...'),
                    ]),
            ]);
    }
}
