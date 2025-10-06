<?php

namespace App\Services;

use Exception;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class AIService
{
    /**
     * Extract articles from HTML content using AI
     */
    public function extractArticlesFromHtml(string $html, string $sourceUrl): array
    {
        try {
            // Clean and truncate HTML content to fit within token limits
            $cleanedContent = $this->cleanHtmlContent($html);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                ->withSystemPrompt($this->getExtractionSystemPrompt())
                ->withPrompt($this->getExtractionPrompt($cleanedContent, $sourceUrl))
                ->generate();

            // Clean the response - remove markdown code blocks if present
            $cleanedResponse = trim($response->text);
            if (str_starts_with($cleanedResponse, '```json')) {
                $cleanedResponse = substr($cleanedResponse, 7);
            }
            if (str_starts_with($cleanedResponse, '```')) {
                $cleanedResponse = substr($cleanedResponse, 3);
            }
            if (str_ends_with($cleanedResponse, '```')) {
                $cleanedResponse = substr($cleanedResponse, 0, -3);
            }
            $cleanedResponse = trim($cleanedResponse);

            $result = json_decode($cleanedResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI: '.json_last_error_msg().'. Raw response: '.substr($response->text, 0, 500));
            }

            return $result['articles'] ?? [];
        } catch (Exception $e) {
            throw new Exception('AI extraction failed: '.$e->getMessage());
        }
    }

    /**
     * Generate article content using AI
     */
    public function generateArticle(array $data): array
    {
        try {
            $prompt = $this->getGenerationPrompt($data);

            \Log::info('AI Generation Input:', [
                'title' => $data['scraped_article']['title'] ?? 'Unknown',
                'full_content_length' => strlen($data['full_content'] ?? ''),
                'content_summary_length' => strlen($data['scraped_article']['content_summary'] ?? ''),
                'using_full_content' => isset($data['full_content']) && strlen($data['full_content']) > 100,
            ]);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-5')
                ->withSystemPrompt($this->getGenerationSystemPrompt($data['author']))
                ->withPrompt($prompt)
                ->withMaxTokens(10000)
                ->generate();

            $raw = trim($response->text);

            if (str_starts_with($raw, '```json')) {
                $raw = substr($raw, 7);
            }
            if (str_starts_with($raw, '```')) {
                $raw = substr($raw, 3);
            }
            if (str_ends_with($raw, '```')) {
                $raw = substr($raw, 0, -3);
            }

            $decoded = json_decode($raw, true);

            if (! is_array($decoded)) {
                throw new Exception('AI generation returned invalid JSON. Raw response: '.substr($response->text, 0, 500));
            }

            return [
                'slug' => $decoded['slug'] ?? null,
                'content' => $decoded['content'] ?? '',
                'headline' => $decoded['headline'] ?? null,
                'page_title' => $decoded['page_title'] ?? null,
                'meta_description' => $decoded['meta_description'] ?? null,
                'tags' => isset($decoded['tags']) && is_array($decoded['tags'])
                    ? array_filter(array_map('trim', $decoded['tags']))
                    : [],
            ];
        } catch (Exception $e) {
            throw new Exception('AI generation failed: '.$e->getMessage());
        }
    }

    /**
     * Get research data for article
     */
    public function getResearchData(string $title, string $originalContent): array
    {
        try {
            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                ->withSystemPrompt('You are a film research assistant. Provide factual information about films.')
                ->withPrompt($this->getResearchPrompt($title, $originalContent))
                ->generate();

            $result = json_decode($response->text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }

            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    private function cleanHtmlContent(string $html): string
    {
        // Remove unwanted tags and their content
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);
        $html = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $html);
        $html = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $html);
        $html = preg_replace('/<noscript[^>]*>.*?<\/noscript>/is', '', $html);

        // Keep structural HTML tags that help AI understand content structure
        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><ul><ol><li><a><div><span><table><tr><td><th><blockquote>';

        // Strip unwanted tags but keep structural ones
        $text = strip_tags($html, $allowedTags);

        // Remove excessive whitespace but preserve some structure
        $text = preg_replace('/\s+/', ' ', $text);

        // Clean up empty tags
        $text = preg_replace('/<([^>]+)>\s*<[^>]*>/', '<$1>', $text);

        // Limit to reasonable size (around 15,000 characters to account for HTML tags)
        return substr(trim($text), 0, 15000);
    }

    private function getExtractionSystemPrompt(): string
    {
        return 'You are an expert at extracting film-related articles from web pages. '.
               'You analyze HTML content with structural tags (headings, paragraphs, lists) and identify articles about movies, reviews, news, and streaming. '.
               'Use the HTML structure to better understand content hierarchy and context. '.
               'Return ONLY valid JSON without any markdown formatting, code blocks, or explanations. '.
               'If no film articles are found, return {"articles": []}.';
    }

    private function getExtractionPrompt(string $html, string $sourceUrl): string
    {
        return <<<PROMPT
Analyze this HTML content from {$sourceUrl} and extract all film-related articles.
The content includes structural HTML tags (headings, paragraphs, lists) that help identify article structure.

Requirements:
- Only extract articles about movies, film reviews, news, or streaming content
- Use HTML structure (h1, h2, p, ul, li tags) to understand content hierarchy
- Skip navigation, ads, comments, and non-film content
- Extract the main article content (first 2-3 paragraphs)
- Include publication date if available
- Return ONLY valid JSON in this exact format without any additional text:

{
  "articles": [
    {
      "title": "Article Title",
      "content_summary": "First 2-3 paragraphs of content...",
      "author_name": "Author Name (if available)",
      "published_at": "2024-01-01 10:00:00",
      "image_url": "https://example.com/image.jpg (if available)",
      "article_url": "https://example.com/article-url"
    }
  ]
}

HTML Content:
{$html}
PROMPT;
    }

    private function getGenerationSystemPrompt(array $author): string
    {
        return "You are {$author['name']}, a {$author['specialization']} film critic. ".
               "Write in Czech language. Your personality: {$author['personality_prompt']}. ".
               "Writing style: {$author['writing_style_prompt']}. ".
               'Always include Czech film context and streaming availability when relevant. '.
               'Respond ONLY with valid JSON containing: slug, content (HTML), headline, page_title, meta_description, tags (array).';
    }

    private function getGenerationPrompt(array $data): string
    {
        $research = $data['research'] ?? [];
        $scrapedArticle = $data['scraped_article'];

        $title = $scrapedArticle['title'] ?? '';
        $fullContent = $data['full_content'] ?? $scrapedArticle['content_summary'] ?? '';
        $authorName = $data['author']['name'] ?? '';
        $imdbId = $research['imdb_id'] ?? 'Unknown';
        $genres = is_array($research['genres'] ?? null) ? implode(', ', $research['genres']) : ($research['genres'] ?? 'Unknown');
        $czechRelease = $research['czech_release_date'] ?? 'Unknown';
        $streaming = is_array($research['streaming_platforms'] ?? null) ? implode(', ', $research['streaming_platforms']) : ($research['streaming_platforms'] ?? 'Unknown');
        $funFact = $research['fun_fact'] ?? 'Unknown';

        // Use full HTML content for AI - it will extract the article content itself
        $keyContent = $fullContent;

        return <<<PROMPT
Analyze the provided article content and additional research data. Then produce ONLY JSON with the following structure:
{
  "slug": "...",
  "content": "<p>…</p>",
  "headline": "…",
  "page_title": "…",
  "meta_description": "…",
  "tags": ["tag1", "tag2", …]
}

Requirements:
- "slug" must be URL-safe (lowercase, hyphen separated, max 80 chars).
- "content" musí byť HTML (odseky, nadpisy) s rozsahom 400-600 slov v češtine.
- "headline" musí byť chytľavý SEO titul, max 60 znakov.
- "page_title" max 60 znakov, vhodný pre title tag.
- "meta_description" 140-160 znakov, vystihuje článok.
- "tags" obsahuje 3-6 krátkych kľúčových fráz (bez #).
- Streaming info spomeň iba, ak to text priamo obsahuje.
- Nepoužívaj markdown, iba HTML v "content".

Relevantné dáta:
Title: {$title}
Article Content: {$keyContent}
Additional Research:
- Genres: {$genres}
- Czech Release: {$czechRelease}
- Streaming Availability (if known): {$streaming}
PROMPT;
    }

    private function getResearchPrompt(string $title, string $originalContent): string
    {
        return <<<PROMPT
Research this film article and provide structured data:

Title: {$title}
Content: {$originalContent}

Return JSON with:
{
  "imdb_id": "tt0111161",
  "genres": ["Drama", "Crime"],
  "czech_release": "2024-03-15",
  "streaming": ["Netflix", "HBO Max"],
  "fun_fact": "Interesting fact about the film..."
}
PROMPT;
    }
}
