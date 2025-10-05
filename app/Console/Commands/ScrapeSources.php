<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeSources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:sources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape all active sources for film articles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scraping of active sources...');

        $sources = \App\Models\Source::where('active', true)->get();

        if ($sources->isEmpty()) {
            $this->warn('No active sources found.');
            return;
        }

        $this->info("Found {$sources->count()} active sources to scrape.");

        $progressBar = $this->output->createProgressBar($sources->count());
        $progressBar->start();

        $totalArticles = 0;

        foreach ($sources as $source) {
            try {
                $articlesCount = $this->scrapeSource($source);
                $totalArticles += $articlesCount;

                $this->info(" {$source->name}: {$articlesCount} articles");
            } catch (\Exception $e) {
                $this->error("Failed to scrape {$source->name}: {$e->getMessage()}");
            }

            $progressBar->advance();
            sleep(3); // Respectful delay between requests
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Scraping completed. Total articles extracted: {$totalArticles}");
    }

    private function scrapeSource(\App\Models\Source $source): int
    {
        // Download HTML content
        $html = $this->downloadHtml($source->url);

        if (!$html) {
            return 0;
        }

        // Extract articles using AI
        $aiService = app(\App\Services\AIService::class);
        $extractedArticles = $aiService->extractArticlesFromHtml($html, $source->url);

        // Save extracted articles
        $savedCount = 0;
        foreach ($extractedArticles as $articleData) {
            try {
                \App\Models\ScrapedArticle::create([
                    'title' => $articleData['title'] ?? '',
                    'content_summary' => $articleData['content_summary'] ?? '',
                    'author_name' => $articleData['author_name'] ?? null,
                    'published_at' => isset($articleData['published_at'])
                        ? \Carbon\Carbon::parse($articleData['published_at'])
                        : now(),
                    'image_url' => $articleData['image_url'] ?? null,
                    'original_url' => $articleData['article_url'] ?? $source->url,
                    'html_snapshot' => $html,
                    'status' => 'pending',
                    'source_id' => $source->id,
                ]);
                $savedCount++;
            } catch (\Exception $e) {
                // Log error but continue with other articles
                \Log::warning("Failed to save article from {$source->name}: {$e->getMessage()}");
            }
        }

        // Update last checked timestamp
        $source->update(['last_checked_at' => now()]);

        return $savedCount;
    }

    private function downloadHtml(string $url): ?string
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'MovieContext-Bot/1.0 (+https://moviecontext.test)',
                    'header' => 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ]
            ]);

            return file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            \Log::warning("Failed to download HTML from {$url}: {$e->getMessage()}");
            return null;
        }
    }
}
