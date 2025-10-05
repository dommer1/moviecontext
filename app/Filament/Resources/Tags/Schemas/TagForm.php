<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'genre' => 'Žáner',
                        'actor' => 'Herec',
                        'director' => 'Režisér',
                        'theme' => 'Téma',
                    ])
                    ->required(),
            ]);
    }
}
