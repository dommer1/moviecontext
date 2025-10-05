<?php

namespace App\Filament\Resources\Sources\Schemas;

use Filament\Schemas\Schema;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('url')
                    ->required()
                    ->url()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'news' => 'News',
                        'review' => 'Review',
                        'streaming' => 'Streaming',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('language')
                    ->options([
                        'cs' => 'Czech',
                        'en' => 'English',
                        'sk' => 'Slovak',
                    ])
                    ->default('cs')
                    ->required(),
                \Filament\Forms\Components\Toggle::make('active')
                    ->default(true),
                \Filament\Forms\Components\TextInput::make('last_checked_at')
                    ->disabled(),
            ]);
    }
}
