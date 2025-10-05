<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSelectedContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate articles from selected scraped content';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting content generation...');

        // Get selected articles for generation
        $selectedArticles = \App\Models\ScrapedArticle::where('status', 'selected_for_generation')
            ->with('source')
            ->get();

        if ($selectedArticles->isEmpty()) {
            $this->warn('No articles selected for generation. Run "content:select" first.');
            return;
        }

        $this->info("Found {$selectedArticles->count()} articles to generate.");

        $progressBar = $this->output->createProgressBar($selectedArticles->count());
        $progressBar->start();

        $generatedCount = 0;
        $failedCount = 0;

        foreach ($selectedArticles as $scrapedArticle) {
            try {
                $this->info("Generating: {$scrapedArticle->title}");

                $generationService = app(\App\Services\ContentGenerationService::class);
                $article = $generationService->generateArticle($scrapedArticle);

                if ($article) {
                    $generatedCount++;
                    $this->info("✓ Generated: {$article->title}");
                } else {
                    $failedCount++;
                    $this->error("✗ Failed to generate: {$scrapedArticle->title}");
                }

            } catch (\Exception $e) {
                $failedCount++;
                $this->error("✗ Error generating {$scrapedArticle->title}: {$e->getMessage()}");

                // Mark as error
                $scrapedArticle->update(['status' => 'error']);
            }

            $progressBar->advance();

            // Small delay to avoid overwhelming the AI service
            sleep(2);
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Content generation completed:");
        $this->info("✓ Generated: {$generatedCount} articles");
        $this->info("✗ Failed: {$failedCount} articles");

        if ($generatedCount > 0) {
            // Run quality validation on generated articles
            $this->validateGeneratedArticles();
        }
    }

    /**
     * Run quality validation on newly generated articles
     */
    private function validateGeneratedArticles(): void
    {
        $this->info('Running quality validation...');

        // Get articles generated in this session (published_at is null until validation)
        $newArticles = \App\Models\Article::whereNull('published_at')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->get();

        $passedCount = 0;
        $failedCount = 0;

        foreach ($newArticles as $article) {
            if ($this->validateArticle($article)) {
                // Publish the article
                $article->update(['published_at' => now()]);
                $passedCount++;
                $this->info("✓ Published: {$article->title}");
            } else {
                // Mark for manual review
                $article->update(['published_at' => null]); // Keep null for manual review
                $failedCount++;
                $this->warn("⚠ Needs review: {$article->title}");
            }
        }

        $this->info("Quality validation completed:");
        $this->info("✓ Published: {$passedCount} articles");
        $this->info("⚠ Needs review: {$failedCount} articles");
    }

    /**
     * Validate article quality
     */
    private function validateArticle(\App\Models\Article $article): bool
    {
        // Check minimum word count (600 words)
        $wordCount = str_word_count(strip_tags($article->content));
        if ($wordCount < 600) {
            return false;
        }

        // Check if content is in Czech
        $czechWords = ['film', 'režisér', 'herec', 'kina', 'divák', 'scénář', 'natočený', 'premiera'];
        $content = strtolower($article->content);
        $czechWordCount = 0;

        foreach ($czechWords as $word) {
            if (str_contains($content, $word)) {
                $czechWordCount++;
            }
        }

        // Require at least 3 Czech words to confirm it's in Czech
        if ($czechWordCount < 3) {
            return false;
        }

        // Check for featured image
        if (!$article->featured_image_path) {
            // Try to download image from scraped article
            if ($article->scrapedArticle && $article->scrapedArticle->image_url) {
                $imagePath = $this->downloadAndOptimizeImage($article->scrapedArticle->image_url);
                if ($imagePath) {
                    $article->update(['featured_image_path' => $imagePath]);
                }
            }
        }

        // Check for minimum tags
        if ($article->tags()->count() < 3) {
            return false;
        }

        // Check metadata exists
        if (!$article->metadata) {
            return false;
        }

        return true;
    }

    /**
     * Download and optimize featured image
     */
    private function downloadAndOptimizeImage(string $url): ?string
    {
        try {
            // For now, just return the URL - image optimization would be implemented later
            return $url;
        } catch (\Exception $e) {
            \Log::warning("Failed to download image {$url}: {$e->getMessage()}");
            return null;
        }
    }
}
