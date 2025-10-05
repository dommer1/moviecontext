<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'genre' => 'success',
                        'actor' => 'warning',
                        'director' => 'info',
                        'theme' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'genre' => 'Žáner',
                        'actor' => 'Herec',
                        'director' => 'Režisér',
                        'theme' => 'Téma',
                    }),
                TextColumn::make('articles_count')
                    ->counts('articles')
                    ->label('Articles'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'genre' => 'Žáner',
                        'actor' => 'Herec',
                        'director' => 'Režisér',
                        'theme' => 'Téma',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
