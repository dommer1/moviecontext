<?php

namespace App\Filament\Resources\Sources\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'news' => 'success',
                        'review' => 'warning',
                        'streaming' => 'info',
                    }),
                TextColumn::make('language')
                    ->badge(),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('last_checked_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('scraped_articles_count')
                    ->counts('scrapedArticles')
                    ->label('Articles'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'news' => 'News',
                        'review' => 'Review',
                        'streaming' => 'Streaming',
                    ]),
                SelectFilter::make('language')
                    ->options([
                        'cs' => 'Czech',
                        'en' => 'English',
                        'sk' => 'Slovak',
                    ]),
                SelectFilter::make('active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                Action::make('test_scrape')
                    ->label('Test Scrape')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function ($record) {
                        // Run scrape:single command with no interaction
                        \Artisan::call('scrape:single', [
                            'source_id' => $record->id,
                            '--no-interaction' => true,
                        ]);

                        // Get command output
                        $output = \Artisan::output();

                        \Filament\Notifications\Notification::make()
                            ->title('Scraping completed')
                            ->body('Command output: '.$output)
                            ->success()
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
