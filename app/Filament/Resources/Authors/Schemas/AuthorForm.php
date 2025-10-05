<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Schemas\Schema;

class AuthorForm
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
                \Filament\Forms\Components\Textarea::make('bio')
                    ->required()
                    ->maxLength(1000),
                \Filament\Forms\Components\FileUpload::make('avatar_path')
                    ->image()
                    ->directory('authors'),
                \Filament\Forms\Components\Select::make('specialization')
                    ->options([
                        'professional_critic' => 'Profesionálny kritik',
                        'enthusiastic_blogger' => 'Nadšený blogger',
                        'skeptical_expert' => 'Skeptický expert',
                    ])
                    ->required(),
                \Filament\Forms\Components\Textarea::make('personality_prompt')
                    ->required()
                    ->maxLength(2000),
                \Filament\Forms\Components\Textarea::make('writing_style_prompt')
                    ->required()
                    ->maxLength(2000),
                \Filament\Forms\Components\Toggle::make('active')
                    ->default(true),
            ]);
    }
}
