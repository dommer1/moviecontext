<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for SEO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemapContent = $this->generateSitemapXml();

        $sitemapPath = public_path('sitemap.xml');
        file_put_contents($sitemapPath, $sitemapContent);

        $this->info("✓ Sitemap generated at: {$sitemapPath}");
    }

    /**
     * Generate XML sitemap content
     */
    private function generateSitemapXml(): string
    {
        $urls = [];

        // Add homepage
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toISOString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Add published articles
        $articles = \App\Models\Article::published()
            ->orderBy('published_at', 'desc')
            ->get();

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => url('/' . $article->slug),
                'lastmod' => $article->published_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Add author pages
        $authors = \App\Models\Author::active()->get();
        foreach ($authors as $author) {
            $urls[] = [
                'loc' => url('/autor/' . $author->slug),
                'lastmod' => $author->updated_at->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Add tag pages
        $tags = \App\Models\Tag::all();
        foreach ($tags as $tag) {
            $urls[] = [
                'loc' => url('/tag/' . $tag->slug),
                'lastmod' => $tag->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.4',
            ];
        }

        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
