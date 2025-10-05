<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SelectContentForGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:select';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Select scraped articles for content generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Selecting articles for generation...');

        // Get pending scraped articles from last 24 hours
        $candidateArticles = \App\Models\ScrapedArticle::where('status', 'pending')
            ->where('published_at', '>=', now()->subDay())
            ->orderBy('published_at', 'desc')
            ->get();

        if ($candidateArticles->isEmpty()) {
            $this->warn('No pending articles found from the last 24 hours.');
            return;
        }

        $this->info("Found {$candidateArticles->count()} candidate articles.");

        // Score and select articles
        $scoredArticles = $this->scoreArticles($candidateArticles);

        // Select 1-2 top articles (randomly choose between 1 or 2)
        $articlesToGenerate = collect($scoredArticles)
            ->sortByDesc('score')
            ->take(rand(1, 2));

        if ($articlesToGenerate->isEmpty()) {
            $this->warn('No suitable articles found for generation.');
            return;
        }

        $this->info("Selected {$articlesToGenerate->count()} articles for generation:");
        $this->newLine();

        foreach ($articlesToGenerate as $article) {
            $this->line("• {$article['title']} (Score: {$article['score']})");
            $this->line("  Source: {$article['source_name']}");
            $this->line("  Published: {$article['published_at']->format('Y-m-d H:i')}");
            $this->newLine();
        }

        // Mark selected articles as selected for generation
        $selectedIds = $articlesToGenerate->pluck('id');
        \App\Models\ScrapedArticle::whereIn('id', $selectedIds)
            ->update(['status' => 'selected_for_generation']);

        $this->info("Marked {$selectedIds->count()} articles as selected for generation.");
    }

    /**
     * Score articles based on various criteria
     */
    private function scoreArticles($articles): array
    {
        $scored = [];

        foreach ($articles as $article) {
            $score = 0;
            $reasons = [];

            // Base score for being recent
            $hoursOld = now()->diffInHours($article->published_at);
            if ($hoursOld < 6) {
                $score += 30;
                $reasons[] = 'Very recent (< 6 hours)';
            } elseif ($hoursOld < 12) {
                $score += 20;
                $reasons[] = 'Recent (< 12 hours)';
            } elseif ($hoursOld < 24) {
                $score += 10;
                $reasons[] = 'Recent (< 24 hours)';
            }

            // Check for film-related keywords
            $content = strtolower($article->title . ' ' . $article->content_summary);
            $filmKeywords = ['film', 'movie', 'recenz', 'review', 'kino', 'herec', 'režisér', 'trailer'];

            $keywordMatches = 0;
            foreach ($filmKeywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $keywordMatches++;
                }
            }

            if ($keywordMatches >= 3) {
                $score += 25;
                $reasons[] = 'Strong film keywords';
            } elseif ($keywordMatches >= 2) {
                $score += 15;
                $reasons[] = 'Good film keywords';
            } elseif ($keywordMatches >= 1) {
                $score += 5;
                $reasons[] = 'Some film keywords';
            }

            // Prefer Czech/Slovak content
            if ($article->source->language === 'cs' || $article->source->language === 'sk') {
                $score += 15;
                $reasons[] = 'Czech/Slovak content';
            }

            // Prefer review content over news
            if (str_contains($content, 'recenz') || str_contains($content, 'review')) {
                $score += 10;
                $reasons[] = 'Review content';
            }

            // Avoid duplicates (check if similar articles already exist)
            $existingSimilar = \App\Models\Article::where('title', 'LIKE', '%' . substr($article->title, 0, 20) . '%')
                ->where('published_at', '>=', now()->subDays(7))
                ->exists();

            if ($existingSimilar) {
                $score -= 20;
                $reasons[] = 'Potential duplicate';
            }

            // Length check - prefer substantial content
            if (strlen($article->content_summary) > 500) {
                $score += 10;
                $reasons[] = 'Substantial content';
            } elseif (strlen($article->content_summary) < 100) {
                $score -= 10;
                $reasons[] = 'Too short content';
            }

            $scored[] = [
                'id' => $article->id,
                'title' => $article->title,
                'score' => $score,
                'reasons' => $reasons,
                'source_name' => $article->source->name,
                'published_at' => $article->published_at,
            ];
        }

        return $scored;
    }
}
