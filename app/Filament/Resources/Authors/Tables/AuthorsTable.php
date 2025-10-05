<?php

namespace App\Filament\Resources\Authors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuthorsTable
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
                TextColumn::make('specialization')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'professional_critic' => 'success',
                        'enthusiastic_blogger' => 'warning',
                        'skeptical_expert' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'professional_critic' => 'Profesionálny kritik',
                        'enthusiastic_blogger' => 'Nadšený blogger',
                        'skeptical_expert' => 'Skeptický expert',
                    }),
                TextColumn::make('articles_count')
                    ->counts('articles')
                    ->label('Articles'),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('specialization')
                    ->options([
                        'professional_critic' => 'Profesionálny kritik',
                        'enthusiastic_blogger' => 'Nadšený blogger',
                        'skeptical_expert' => 'Skeptický expert',
                    ]),
                SelectFilter::make('active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
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
