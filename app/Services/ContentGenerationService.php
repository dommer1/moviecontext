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
            // Select appropriate author
            $author = $this->selectAuthorForArticle($scrapedArticle);

            // Gather research data
            $researchData = $this->researchService->gatherFilmData(
                $scrapedArticle->title,
                $scrapedArticle->content_summary
            );

            // Generate content using AI
            $generatedContent = $this->aiService->generateArticle([
                'author' => $author->toArray(),
                'scraped_article' => $scrapedArticle->toArray(),
                'research' => $researchData,
            ]);

            if (! $generatedContent) {
                throw new Exception('AI generated empty content');
            }

            // Create article
            $article = Article::create([
                'title' => $this->generateTitle($scrapedArticle, $researchData),
                'slug' => $this->generateSlug($scrapedArticle->title),
                'content' => $generatedContent,
                'excerpt' => $this->generateExcerpt($generatedContent),
                'seo_title' => $this->generateSEOTitle($scrapedArticle->title),
                'seo_description' => $this->generateSEODescription($generatedContent),
                'author_id' => $author->id,
                'scraped_article_id' => $scrapedArticle->id,
            ]);

            // Generate and attach tags
            $this->attachTags($article, $scrapedArticle, $researchData);

            // Create metadata
            $this->createMetadata($article, $researchData);

            // Mark scraped article as processed
            $scrapedArticle->update(['status' => 'processed']);

            return $article;

        } catch (Exception $e) {
            // Mark as error
            $scrapedArticle->update(['status' => 'error']);
            \Log::error("Article generation failed for scraped article {$scrapedArticle->id}: {$e->getMessage()}");

            return null;
        }
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
