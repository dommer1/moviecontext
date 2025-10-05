<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('slug')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('published_at')
                    ->label('Published')
                    ->boolean(fn ($record) => $record->published_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tags_count')
                    ->counts('tags')
                    ->label('Tags'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name'),
                SelectFilter::make('published')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('published_at');
                        } elseif ($data['value'] === '0') {
                            return $query->whereNull('published_at');
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->published_at === null)
                    ->action(function ($record) {
                        $record->update(['published_at' => now()]);
                        \Filament\Notifications\Notification::make()
                            ->title('Article published')
                            ->success()
                            ->send();
                    }),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->published_at !== null)
                    ->action(function ($record) {
                        $record->update(['published_at' => null]);
                        \Filament\Notifications\Notification::make()
                            ->title('Article unpublished')
                            ->warning()
                            ->send();
                    }),
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
