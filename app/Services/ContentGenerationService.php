<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleMetadata;
use App\Models\Author;
use App\Models\ScrapedArticle;
use App\Models\Tag;
use Exception;
use Illuminate\Support\Str;

class ContentGenerationService
{
    public function __construct(
        private AIService $aiService,
        private ResearchService $researchService
    ) {}

    /**
     * Generate article from scraped article
     */
    public function generateArticle(ScrapedArticle $scrapedArticle): ?Article
    {
        try {
            $author = $this->selectAuthorForArticle($scrapedArticle);

            $contentData = $this->getFullArticleContent($scrapedArticle);
            $articleText = $contentData['text'];
            $embeds = $contentData['embeds'];

            $researchData = $this->researchService->gatherFilmData(
                $scrapedArticle->title,
                $articleText
            );

            $generationResult = $this->aiService->generateArticle([
                'author' => $author->toArray(),
                'scraped_article' => $scrapedArticle->toArray(),
                'full_content' => $articleText,
                'research' => $researchData,
            ]);

            $content = $generationResult['content'] ?? '';
            if (empty($content)) {
                throw new Exception('AI returned empty content field.');
            }

            if (! empty($embeds)) {
                $content .= "\n\n".implode("\n\n", $embeds);
            }

            $slug = $generationResult['slug'] ?? null;
            if ($slug) {
                $slug = Str::of($slug)->slug('-')->limit(80, '');
            }

            $article = Article::create([
                'title' => $generationResult['headline'] ?? $this->generateTitle($scrapedArticle, $researchData),
                'slug' => $slug && ! Article::where('slug', $slug)->exists()
                    ? $slug
                    : $this->generateSlug($scrapedArticle->title),
                'content' => $content,
                'excerpt' => $this->generateExcerpt($content),
                'seo_title' => $generationResult['page_title'] ?? $this->generateSEOTitle($scrapedArticle->title),
                'seo_description' => $generationResult['meta_description'] ?? $this->generateSEODescription($content),
                'author_id' => $author->id,
                'scraped_article_id' => $scrapedArticle->id,
            ]);

            if (! empty($generationResult['tags'])) {
                $tags = collect($generationResult['tags'])
                    ->map(function ($tag) {
                        $name = ucfirst($tag);
                        $slug = Str::slug($tag);

                        $existing = Tag::where('slug', $slug)->first();
                        if ($existing) {
                            return $existing->id;
                        }

                        $knownTypes = ['genre', 'actor', 'director'];
                        $assignedType = $knownTypes[array_rand($knownTypes)];

                        return Tag::create([
                            'name' => $name,
                            'slug' => $slug,
                            'type' => $assignedType,
                        ])->id;
                    });

                $article->tags()->sync($tags);
            }

            $this->createMetadata($article, $researchData);

            $scrapedArticle->update(['status' => 'processed']);

            return $article;
        } catch (Exception $e) {
            $scrapedArticle->update(['status' => 'error']);
            \Log::error("Article generation failed for scraped article {$scrapedArticle->id}: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Get full article content from the original URL
     */
    private function getFullArticleContent(ScrapedArticle $scrapedArticle): array
    {
        if ($scrapedArticle->original_url) {
            try {
                $html = $this->downloadArticleHtml($scrapedArticle->original_url);
                if ($html) {
                    $processed = $this->cleanHtmlForAI($html, $scrapedArticle->title);
                    if (strlen($processed['text']) > 300) {
                        return $processed;
                    }
                }
            } catch (Exception $e) {
                \Log::warning("Failed to download full article from {$scrapedArticle->original_url}: {$e->getMessage()}");
            }
        }

        if ($scrapedArticle->html_snapshot && strlen($scrapedArticle->html_snapshot) > 0) {
            $processed = $this->cleanHtmlForAI($scrapedArticle->html_snapshot, $scrapedArticle->title);
            if (strlen($processed['text']) > 300) {
                return $processed;
            }
        }

        return [
            'text' => $scrapedArticle->content_summary ?? '',
            'embeds' => [],
        ];
    }

    /**
     * Extract article content from HTML for AI processing
     */
    private function cleanHtmlForAI(string $html, ?string $title = null): array
    {
        $embeds = [];

        if (preg_match_all('/<iframe[^>]*src="([^"]+)"[^>]*><\/iframe>/i', $html, $iframeMatches)) {
            foreach ($iframeMatches[1] as $src) {
                if (preg_match('/youtube\.com|vimeo\.com|player\.csfd\.cz|embed/iu', $src)) {
                    $embeds[] = '<iframe src="'.$src.'" loading="lazy" referrerpolicy="no-referrer" allowfullscreen class="w-full max-w-3xl mx-auto aspect-video"></iframe>';
                }
            }
        }

        if (preg_match_all('/<video[^>]*>(.*?)<\/video>/is', $html, $videoMatches)) {
            foreach ($videoMatches[0] as $videoHtml) {
                $embeds[] = '<div class="w-full max-w-3xl mx-auto">'.strip_tags($videoHtml, '<video><source>');
            }
        }

        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);
        $html = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $html);
        $html = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $html);
        $html = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $html);
        $html = preg_replace('/<!--.*?-->/is', '', $html);

        if ($title) {
            $titlePosition = stripos($html, $title);
            if ($titlePosition !== false) {
                $html = substr($html, $titlePosition);
            }
        }

        $paragraphs = [];
        if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
            foreach ($matches[1] as $paragraph) {
                $clean = trim(strip_tags($paragraph));
                if (strlen($clean) > 50) {
                    $paragraphs[] = $clean;
                }
            }
        }

        if (empty($paragraphs)) {
            $paragraphs[] = strip_tags($html);
        }

        $plainText = implode("\n\n", $paragraphs);

        $plainText = preg_replace('/Všimli sme si[^\n]+CSFD\.sk\?/iu', '', $plainText);
        $plainText = str_ireplace('zpět na všechny novinky', '', $plainText);

        $plainText = preg_replace('/\s+/', ' ', $plainText);
        $plainText = preg_replace('/(\.|\?|!)\s*(?=[A-ZÁČĎÉĚÍĹĽŇÓÔŘŔŠŤÚŮÝŽ])/u', "$1 \n\n", $plainText);

        $plainText = substr(trim($plainText), 0, 4000);

        return [
            'text' => $plainText,
            'embeds' => $embeds,
        ];
    }

    /**
     * Download HTML content from article URL
     */
    private function downloadArticleHtml(string $url): ?string
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'user_agent' => 'MovieContext-Bot/1.0',
                    'header' => 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ],
            ]);

            return file_get_contents($url, false, $context);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extract specific article content from HTML using article information
     */
    private function extractSpecificArticleContent(string $html, ScrapedArticle $scrapedArticle): string
    {
        // Remove unwanted elements
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);
        $html = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $html);
        $html = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $html);
        $html = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $html);

        // If this is a feed page (like the html_snapshot), try to find the specific article
        if ($this->isFeedPage($html)) {
            return $this->extractArticleFromFeed($html, $scrapedArticle);
        }

        // If this is a single article page, extract the main content
        return $this->extractArticleContentFromHtml($html);
    }

    /**
     * Check if HTML contains a feed/list of articles
     */
    private function isFeedPage(string $html): bool
    {
        // Look for multiple article indicators
        $articleCount = preg_match_all('/<article[^>]*>/i', $html);
        $headingCount = preg_match_all('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', $html);

        // If there are multiple articles or headings, it's likely a feed
        return $articleCount > 3 || $headingCount > 5;
    }

    /**
     * Extract specific article from a feed page
     */
    private function extractArticleFromFeed(string $html, ScrapedArticle $scrapedArticle): string
    {
        // Try to find the article by URL
        $urlPattern = preg_quote(basename($scrapedArticle->original_url), '/');
        $urlParts = parse_url($scrapedArticle->original_url);
        $path = $urlParts['path'] ?? '';

        // Look for content near the article URL or title
        $titlePattern = preg_quote($scrapedArticle->title, '/');

        // Find the position of the article title in HTML
        $titlePos = stripos($html, $scrapedArticle->title);
        if ($titlePos !== false) {
            // Extract content around the title (next 2000 characters should contain the article summary)
            $start = max(0, $titlePos - 500);
            $length = 2500;
            $articleSection = substr($html, $start, $length);

            // Remove other article titles to avoid confusion
            $articleSection = preg_replace('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', '', $articleSection);

            $content = strip_tags($articleSection, '<p><br><strong><b><em><i>');
            $content = preg_replace('/\s+/', ' ', $content);
            $content = trim($content);

            if (strlen($content) > 100) {
                return substr($content, 0, 2000);
            }
        }

        // Fallback to the original extraction method
        return $this->extractArticleContentFromHtml($html);
    }

    /**
     * Extract article content from a full article page (not from a feed)
     */
    private function extractArticleContentFromFullPage(string $html): string
    {
        // Remove unwanted elements
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);
        $html = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $html);
        $html = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $html);
        $html = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $html);

        // For article pages, try to find the main content area
        $patterns = [
            '/<article[^>]*>(.*?)<\/article>/is',
            '/<div[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<div[^>]*class="[^"]*article[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<div[^>]*class="[^"]*post[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<main[^>]*>(.*?)<\/main>/is',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $content = $matches[1];
                if (strlen($content) > 500) { // Need substantial content
                    // Clean and return
                    $content = strip_tags($content, '<p><br><strong><b><em><i><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote>');
                    $content = preg_replace('/\s+/', ' ', $content);
                    $content = trim($content);

                    // Return reasonable amount for AI processing
                    return substr($content, 0, 10000);
                }
            }
        }

        // For CSFD and similar sites, try to extract all paragraphs after the title
        if (preg_match('/<h1[^>]*>.*?<\/h1>(.*?)<\/body>/is', $html, $bodyMatch)) {
            $bodyContent = $bodyMatch[1];

            // Extract all paragraphs
            if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $bodyContent, $paraMatches)) {
                $content = '';
                foreach ($paraMatches[1] as $paragraph) {
                    $cleanPara = trim(strip_tags($paragraph));
                    if (strlen($cleanPara) > 50) { // Substantial paragraph
                        $content .= $cleanPara.' ';
                    }
                }

                if (strlen($content) > 300) {
                    return substr(trim($content), 0, 10000);
                }
            }
        }

        // Fallback: try to extract from the whole page but be more selective
        $content = strip_tags($html, '<p><br><strong><b><em><i><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote>');
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        // Try to find the main content by looking for substantial paragraphs
        $paragraphs = preg_split('/<\/p>/i', $content);
        $mainContent = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim(strip_tags($paragraph));
            if (strlen($paragraph) > 100) { // Substantial paragraph
                $mainContent .= $paragraph.' ';
                if (strlen($mainContent) > 3000) {
                    break;
                } // Enough content
            }
        }

        return substr(trim($mainContent), 0, 6000);
    }

    /**
     * Extract article content from HTML (similar to AIService but focused on article content)
     */
    private function extractArticleContentFromHtml(string $html): string
    {
        // Remove unwanted elements
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);
        $html = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $html);
        $html = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $html);
        $html = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $html);

        // Try to find article content using simple regex patterns
        $patterns = [
            '/<article[^>]*>(.*?)<\/article>/is',
            '/<div[^>]*class="[^"]*article[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<div[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<main[^>]*>(.*?)<\/main>/is',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $content = $matches[1];
                if (strlen($content) > 300) {
                    // Clean the content
                    $content = strip_tags($content, '<p><br><strong><b><em><i><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote>');
                    $content = preg_replace('/\s+/', ' ', $content);
                    $content = trim($content);
                    if (strlen($content) > 200) {
                        return substr($content, 0, 3000); // Shorter limit for AI
                    }
                }
            }
        }

        // Fallback: strip all tags and return text content
        $content = strip_tags($html, '<p><br><strong><b><em><i><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote>');
        $content = preg_replace('/\s+/', ' ', $content);

        return substr(trim($content), 0, 3000);
    }

    /**
     * Select appropriate author based on article content
     */
    private function selectAuthorForArticle(ScrapedArticle $scrapedArticle): Author
    {
        $content = strtolower($scrapedArticle->title.' '.$scrapedArticle->content_summary);

        // Determine article type and match with author specialization
        if (str_contains($content, 'recenz') || str_contains($content, 'review')) {
            // For reviews, randomly choose between all authors (sometimes roundtable)
            $authors = Author::active()->get();

            return $authors->random();
        }

        if (str_contains($content, 'novink') || str_contains($content, 'news') || str_contains($content, 'stream')) {
            // News/streaming content - enthusiastic blogger
            return Author::where('specialization', 'enthusiastic_blogger')->first()
                ?? Author::active()->first();
        }

        if (str_contains($content, 'horor') || str_contains($content, 'thriller') || str_contains($content, 'sci-fi')) {
            // Genre content - skeptical expert
            return Author::where('specialization', 'skeptical_expert')->first()
                ?? Author::active()->first();
        }

        // Default to professional critic for other content
        return Author::where('specialization', 'professional_critic')->first()
            ?? Author::active()->first();
    }

    /**
     * Generate article title
     */
    private function generateTitle(ScrapedArticle $scrapedArticle, array $researchData): string
    {
        // Use original title but ensure it's in Czech context
        $title = $scrapedArticle->title;

        // Add Czech context if not present
        if (! str_contains(strtolower($title), 'česk') &&
            ! str_contains(strtolower($title), 'slovensk') &&
            isset($researchData['czech_title'])) {
            $title .= ' - '.$researchData['czech_title'];
        }

        return $title;
    }

    /**
     * Generate URL slug
     */
    private function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure uniqueness
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate article excerpt
     */
    private function generateExcerpt(string $content): string
    {
        // Take first 200 characters, but try to end at sentence
        $excerpt = Str::limit($content, 200);

        // Try to end at last complete sentence
        $sentences = preg_split('/[.!?]+/', $excerpt, -1, PREG_SPLIT_NO_EMPTY);
        if (count($sentences) > 1) {
            array_pop($sentences); // Remove incomplete sentence
            $excerpt = implode('. ', $sentences).'.';
        }

        return $excerpt;
    }

    /**
     * Generate SEO title
     */
    private function generateSEOTitle(string $title): string
    {
        $seoTitle = $title;

        if (strlen($seoTitle) > 60) {
            $seoTitle = Str::limit($seoTitle, 57).'...';
        }

        return $seoTitle;
    }

    /**
     * Generate SEO description
     */
    private function generateSEODescription(string $content): string
    {
        $description = strip_tags($content);
        $description = Str::limit($description, 160);

        return $description;
    }

    /**
     * Attach relevant tags to article
     */
    private function attachTags(Article $article, ScrapedArticle $scrapedArticle, array $researchData): void
    {
        $tags = [];

        // Extract from research data
        if (isset($researchData['genres'])) {
            $genres = is_array($researchData['genres']) ? $researchData['genres'] : [$researchData['genres']];
            foreach ($genres as $genre) {
                $tags[] = $this->findOrCreateTag($genre, 'genre');
            }
        }

        if (isset($researchData['cast']) && is_array($researchData['cast'])) {
            foreach (array_slice($researchData['cast'], 0, 2) as $actor) {
                $tags[] = $this->findOrCreateTag($actor, 'actor');
            }
        }

        if (isset($researchData['director'])) {
            $tags[] = $this->findOrCreateTag($researchData['director'], 'director');
        }

        // Extract from content (simple keyword matching)
        $content = strtolower($scrapedArticle->title.' '.$scrapedArticle->content_summary);

        $themeKeywords = [
            'český film' => 'Český film',
            'slovenský film' => 'Slovenský film',
            'netflix' => 'Netflix',
            'hbo' => 'HBO Max',
            'disney' => 'Disney+',
            'stream' => 'Streaming',
        ];

        foreach ($themeKeywords as $keyword => $tagName) {
            if (str_contains($content, $keyword)) {
                $tags[] = $this->findOrCreateTag($tagName, 'theme');
            }
        }

        // Attach tags to article
        $article->tags()->attach(collect($tags)->pluck('id'));
    }

    /**
     * Find or create tag
     */
    private function findOrCreateTag(string $name, string $type): Tag
    {
        $slug = Str::slug($name);

        return Tag::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'type' => $type]
        );
    }

    /**
     * Create article metadata
     */
    private function createMetadata(Article $article, array $researchData): void
    {
        $metadata = [
            'article_id' => $article->id,
            'imdb_id' => $researchData['imdb_id'] ?? null,
            'csfd_url' => $researchData['csfd_url'] ?? null,
            'czech_release_date' => isset($researchData['czech_release_date'])
                ? $researchData['czech_release_date']
                : null,
            'streaming_platforms' => $researchData['streaming_platforms'] ?? null,
            'fun_fact' => $this->researchService->generateFunFact($researchData),
        ];

        ArticleMetadata::create($metadata);
    }
}
