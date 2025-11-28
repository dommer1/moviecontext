<?php

namespace App\Filament\Resources\ScrapedArticles\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScrapedArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(60),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'selected_for_generation' => 'warning',
                        'processed' => 'success',
                        'error' => 'danger',
                    }),
                TextColumn::make('author_name')
                    ->label('Author')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('content_summary')
                    ->label('Summary')
                    ->limit(100)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('original_url')
                    ->label('URL')
                    ->url(fn ($record) => $record->original_url)
                    ->openUrlInNewTab()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'selected_for_generation' => 'Selected for Generation',
                        'processed' => 'Processed',
                        'error' => 'Error',
                    ]),
                SelectFilter::make('source')
                    ->relationship('source', 'name'),
            ])
            ->recordActions([
                Action::make('select_for_generation')
                    ->label('Select for Generation')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'selected_for_generation']);
                        \Filament\Notifications\Notification::make()
                            ->title('Article selected for generation')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('bulk_select_for_generation')
                        ->label('Select for Generation')
                        ->icon('heroicon-o-check-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = $records->where('status', 'pending')->count();
                            $records->where('status', 'pending')
                                ->each(fn ($record) => $record->update(['status' => 'selected_for_generation']));

                            \Filament\Notifications\Notification::make()
                                ->title("{$count} articles selected for generation")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
