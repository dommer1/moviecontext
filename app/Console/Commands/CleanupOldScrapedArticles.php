<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOldScrapedArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:cleanup-old-scraped';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old scraped articles and temporary data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of old scraped data...');

        // Delete scraped articles older than 30 days that haven't been processed
        $oldScrapedArticles = \App\Models\ScrapedArticle::where('created_at', '<', now()->subDays(30))
            ->where('status', '!=', 'processed')
            ->count();

        if ($oldScrapedArticles > 0) {
            if ($this->confirm("Delete {$oldScrapedArticles} old scraped articles (30+ days old, not processed)?")) {
                \App\Models\ScrapedArticle::where('created_at', '<', now()->subDays(30))
                    ->where('status', '!=', 'processed')
                    ->delete();

                $this->info("✓ Deleted {$oldScrapedArticles} old scraped articles");
            }
        } else {
            $this->info('No old scraped articles to clean up');
        }

        // Clean up error status articles older than 7 days
        $errorArticles = \App\Models\ScrapedArticle::where('status', 'error')
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        if ($errorArticles > 0) {
            if ($this->confirm("Delete {$errorArticles} error articles (7+ days old)?")) {
                \App\Models\ScrapedArticle::where('status', 'error')
                    ->where('created_at', '<', now()->subDays(7))
                    ->delete();

                $this->info("✓ Deleted {$errorArticles} error articles");
            }
        } else {
            $this->info('No error articles to clean up');
        }

        // Clean up old cache entries (if using file cache)
        if (config('cache.default') === 'file') {
            $this->cleanupFileCache();
        }

        // Clean up old log files (keep last 30 days)
        $this->cleanupOldLogs();

        $this->info('Cleanup completed successfully');
    }

    /**
     * Clean up old file cache entries
     */
    private function cleanupFileCache(): void
    {
        $cachePath = storage_path('framework/cache/data');

        if (!is_dir($cachePath)) {
            return;
        }

        $files = glob($cachePath . '/*/*/*/*');
        $deleted = 0;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < strtotime('-7 days')) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("✓ Cleaned up {$deleted} old cache files");
        }
    }

    /**
     * Clean up old log files
     */
    private function cleanupOldLogs(): void
    {
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');
        $deleted = 0;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < strtotime('-30 days')) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("✓ Cleaned up {$deleted} old log files");
        }
    }
}
