<?php

namespace App\Filament\Resources\AffiliateLinks\Schemas;

use Filament\Schemas\Schema;

class AffiliateLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('platform')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('url')
                    ->required()
                    ->url()
                    ->maxLength(255),
                \Filament\Forms\Components\FileUpload::make('logo_path')
                    ->image()
                    ->directory('affiliate-logos')
                    ->maxSize(1024),
                \Filament\Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                \Filament\Forms\Components\Toggle::make('active')
                    ->default(true),
            ]);
    }
}
