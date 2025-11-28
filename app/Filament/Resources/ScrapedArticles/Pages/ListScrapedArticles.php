<?php

namespace App\Filament\Resources\ScrapedArticles\Pages;

use App\Filament\Resources\ScrapedArticles\ScrapedArticleResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScrapedArticles extends ListRecords
{
    protected static string $resource = ScrapedArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('auto_select')
                ->label('Auto Select Articles')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Auto Select Articles for Generation')
                ->modalDescription('This will automatically score and select 1-2 best articles from pending articles (last 24 hours) for content generation, similar to the content:select command.')
                ->action(function () {
                    // Get pending scraped articles from last 24 hours
                    $candidateArticles = \App\Models\ScrapedArticle::where('status', 'pending')
                        ->where('published_at', '>=', now()->subDay())
                        ->with('source')
                        ->orderBy('published_at', 'desc')
                        ->get();

                    if ($candidateArticles->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('No pending articles found')
                            ->body('No pending articles found from the last 24 hours.')
                            ->warning()
                            ->send();

                        return;
                    }

                    // Score articles (same logic as SelectContentForGeneration command)
                    $scored = [];
                    foreach ($candidateArticles as $article) {
                        $score = 0;

                        // Base score for being recent
                        $hoursOld = now()->diffInHours($article->published_at);
                        if ($hoursOld < 6) {
                            $score += 30;
                        } elseif ($hoursOld < 12) {
                            $score += 20;
                        } elseif ($hoursOld < 24) {
                            $score += 10;
                        }

                        // Check for film-related keywords
                        $content = strtolower($article->title.' '.$article->content_summary);
                        $filmKeywords = ['film', 'movie', 'recenz', 'review', 'kino', 'herec', 'režisér', 'trailer'];

                        $keywordMatches = 0;
                        foreach ($filmKeywords as $keyword) {
                            if (str_contains($content, $keyword)) {
                                $keywordMatches++;
                            }
                        }

                        if ($keywordMatches >= 3) {
                            $score += 25;
                        } elseif ($keywordMatches >= 2) {
                            $score += 15;
                        } elseif ($keywordMatches >= 1) {
                            $score += 5;
                        }

                        // Prefer Czech/Slovak content
                        if ($article->source->language === 'cs' || $article->source->language === 'sk') {
                            $score += 15;
                        }

                        // Prefer review content over news
                        if (str_contains($content, 'recenz') || str_contains($content, 'review')) {
                            $score += 10;
                        }

                        // Avoid duplicates
                        $existingSimilar = \App\Models\Article::where('title', 'LIKE', '%'.substr($article->title, 0, 20).'%')
                            ->where('published_at', '>=', now()->subDays(7))
                            ->exists();

                        if ($existingSimilar) {
                            $score -= 20;
                        }

                        // Length check
                        if (strlen($article->content_summary) > 500) {
                            $score += 10;
                        } elseif (strlen($article->content_summary) < 100) {
                            $score -= 10;
                        }

                        $scored[] = [
                            'id' => $article->id,
                            'title' => $article->title,
                            'score' => $score,
                        ];
                    }

                    // Select 1-2 top articles (randomly choose between 1 or 2)
                    $articlesToGenerate = collect($scored)
                        ->sortByDesc('score')
                        ->take(rand(1, 2));

                    if ($articlesToGenerate->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('No suitable articles found')
                            ->body('No suitable articles found for generation.')
                            ->warning()
                            ->send();

                        return;
                    }

                    // Mark selected articles as selected for generation
                    $selectedIds = $articlesToGenerate->pluck('id');
                    \App\Models\ScrapedArticle::whereIn('id', $selectedIds)
                        ->update(['status' => 'selected_for_generation']);

                    $count = $selectedIds->count();
                    $titles = $articlesToGenerate->pluck('title')->join(', ');

                    \Filament\Notifications\Notification::make()
                        ->title("{$count} articles selected for generation")
                        ->body($titles)
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
