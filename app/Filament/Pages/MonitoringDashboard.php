<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use App\Models\Article;
use App\Models\ScrapedArticle;
use App\Models\Source;
use App\Models\Author;
use Illuminate\Support\Facades\DB;

class MonitoringDashboard extends Page
{
    protected static ?string $navigationLabel = 'Monitoring Dashboard';

    protected static ?int $navigationSort = 1;

    public function getTotalArticles()
    {
        return Article::published()->count();
    }

    public function getTotalScrapedArticles()
    {
        return ScrapedArticle::count();
    }

    public function getActiveSources()
    {
        return Source::where('active', true)->count();
    }

    public function getTotalSources()
    {
        return Source::count();
    }

    public function getLastScrapingTime()
    {
        $lastSource = Source::whereNotNull('last_checked_at')
            ->orderBy('last_checked_at', 'desc')
            ->first();

        return $lastSource ? $lastSource->last_checked_at : null;
    }

    public function getLastArticlePublished()
    {
        $lastArticle = Article::published()
            ->orderBy('published_at', 'desc')
            ->first();

        return $lastArticle ? $lastArticle->published_at : null;
    }

    public function getArticlesToday()
    {
        return Article::published()
            ->whereDate('published_at', today())
            ->count();
    }

    public function getArticlesThisWeek()
    {
        return Article::published()
            ->where('published_at', '>=', now()->startOfWeek())
            ->count();
    }

    public function getRecentArticles()
    {
        return Article::with('author')
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(20)
            ->get();
    }

    public function getScrapingStatus()
    {
        return [
            'pending' => ScrapedArticle::where('status', 'pending')->count(),
            'selected_for_generation' => ScrapedArticle::where('status', 'selected_for_generation')->count(),
            'processed' => ScrapedArticle::where('status', 'processed')->count(),
            'error' => ScrapedArticle::where('status', 'error')->count(),
        ];
    }

    public function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
        ];
    }

    public function getTopViewedArticles()
    {
        return Article::published()
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getAuthorStats()
    {
        return Author::withCount(['articles' => function ($query) {
            $query->published();
        }])->get();
    }

    public function getDailyArticleStats()
    {
        return Article::published()
            ->selectRaw('DATE(published_at) as date, COUNT(*) as count')
            ->where('published_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
