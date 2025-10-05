<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeSingleSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:single {source_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape a single source for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceId = $this->argument('source_id');

        $source = \App\Models\Source::find($sourceId);

        if (!$source) {
            $this->error("Source with ID {$sourceId} not found.");
            return 1;
        }

        $this->info("Scraping source: {$source->name} ({$source->url})");

        try {
            // Download HTML content
            $html = $this->downloadHtml($source->url);

            if (!$html) {
                $this->error('Failed to download HTML content.');
                return 1;
            }

            $this->info('HTML downloaded successfully (' . strlen($html) . ' characters)');

            // Extract articles using AI
            $aiService = app(\App\Services\AIService::class);
            $extractedArticles = $aiService->extractArticlesFromHtml($html, $source->url);

            $this->info("AI extracted " . count($extractedArticles) . " articles:");

            foreach ($extractedArticles as $index => $article) {
                $this->line(($index + 1) . ". " . ($article['title'] ?? 'No title'));
                $this->line("   URL: " . ($article['article_url'] ?? 'N/A'));
                $this->line("   Author: " . ($article['author_name'] ?? 'N/A'));
                $this->line("   Summary: " . substr($article['content_summary'] ?? '', 0, 100) . '...');
                $this->newLine();
            }

            // Save to database if confirmed
            if ($this->confirm('Save extracted articles to database?', true)) {
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
                        $this->error("Failed to save article: {$e->getMessage()}");
                    }
                }

                $this->info("Saved {$savedCount} articles to database.");
            }

            // Update last checked timestamp
            $source->update(['last_checked_at' => now()]);

        } catch (\Exception $e) {
            $this->error("Scraping failed: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    private function downloadHtml(string $url): ?string
    {
        $this->info("Downloading HTML from: {$url}");

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
            $this->error("Failed to download HTML: {$e->getMessage()}");
            return null;
        }
    }
}
