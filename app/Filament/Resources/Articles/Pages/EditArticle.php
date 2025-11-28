<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Services\ContentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerate')
                ->label('Pregenerovať článok')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Pregenerovať článok')
                ->modalDescription('Táto akcia znovu vygeneruje obsah článku pomocou AI. Pôvodný obsah bude nahradený novým.')
                ->modalSubmitActionLabel('Pregenerovať')
                ->action(function () {
                    $contentGenerationService = app(ContentGenerationService::class);
                    $success = $contentGenerationService->regenerateArticle($this->record);

                    if ($success) {
                        \Filament\Notifications\Notification::make()
                            ->title('Článok bol úspešne pregenerovaný')
                            ->success()
                            ->send();

                        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Chyba pri pregenerovaní článku')
                            ->body('Skontrolujte logy pre viac informácií.')
                            ->danger()
                            ->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}
