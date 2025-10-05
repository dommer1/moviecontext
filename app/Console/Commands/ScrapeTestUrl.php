<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeTestUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:test {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AI extraction on any URL without saving to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->argument('url');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->error('Invalid URL provided.');
            return 1;
        }

        $this->info("Testing AI extraction on: {$url}");

        try {
            // Download HTML content
            $html = $this->downloadHtml($url);

            if (!$html) {
                $this->error('Failed to download HTML content.');
                return 1;
            }

            $this->info('HTML downloaded successfully (' . strlen($html) . ' characters)');

            // Extract articles using AI
            $aiService = app(\App\Services\AIService::class);
            $extractedArticles = $aiService->extractArticlesFromHtml($html, $url);

            $this->info("AI extracted " . count($extractedArticles) . " articles:");

            foreach ($extractedArticles as $index => $article) {
                $this->line(($index + 1) . ". " . ($article['title'] ?? 'No title'));
                $this->line("   URL: " . ($article['article_url'] ?? 'N/A'));
                $this->line("   Author: " . ($article['author_name'] ?? 'N/A'));
                $this->line("   Published: " . ($article['published_at'] ?? 'N/A'));
                $this->line("   Summary: " . substr($article['content_summary'] ?? '', 0, 200) . '...');
                $this->newLine();
            }

            if (empty($extractedArticles)) {
                $this->warn('No articles were extracted. The AI might not have found any film-related content.');
            }

        } catch (\Exception $e) {
            $this->error("Test failed: {$e->getMessage()}");
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
