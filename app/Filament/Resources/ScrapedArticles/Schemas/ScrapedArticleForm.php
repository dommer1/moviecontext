<?php

namespace App\Filament\Resources\ScrapedArticles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ScrapedArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('content_summary')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('author_name'),
                DateTimePicker::make('published_at')
                    ->required(),
                TextInput::make('image_url')
                    ->label('Image URL')
                    ->url(),
                TextInput::make('original_url')
                    ->url()
                    ->required(),
                Textarea::make('html_snapshot')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'selected_for_generation' => 'Selected for Generation',
                        'processed' => 'Processed',
                        'error' => 'Error',
                    ])
                    ->required()
                    ->default('pending'),
                Select::make('source_id')
                    ->relationship('source', 'name')
                    ->required(),
            ]);
    }
}
