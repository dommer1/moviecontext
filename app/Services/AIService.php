<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class AIService
{
    /**
     * Extract articles from HTML content using AI
     * Optionally suggests author for each article in the same prompt
     */
    public function extractArticlesFromHtml(string $html, string $sourceUrl, ?array $authors = null): array
    {
        try {
            // Clean and truncate HTML content to fit within token limits
            $cleanedContent = $this->cleanHtmlContent($html);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                ->withSystemPrompt($this->getExtractionSystemPrompt($authors !== null))
                ->withPrompt($this->getExtractionPrompt($cleanedContent, $sourceUrl, $authors))
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
                // Try to fix JSON by escaping unescaped quotes in strings
                $fixed = $this->fixJsonString($cleanedResponse);
                $result = json_decode($fixed, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON parsing failed after fix attempt in extraction', [
                        'error' => json_last_error_msg(),
                        'raw_preview' => substr($cleanedResponse, 0, 1000),
                    ]);
                    throw new Exception('Invalid JSON response from AI: '.json_last_error_msg().'. Raw response: '.substr($response->text, 0, 500));
                }
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

            Log::info('AI Generation Input:', [
                'title' => $data['scraped_article']['title'] ?? 'Unknown',
                'full_content_length' => strlen($data['full_content'] ?? ''),
                'content_summary_length' => strlen($data['scraped_article']['content_summary'] ?? ''),
                'using_full_content' => isset($data['full_content']) && strlen($data['full_content']) > 100,
            ]);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
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
            $raw = trim($raw);

            // Try to parse JSON, if it fails, try to fix it
            $decoded = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try to fix JSON by escaping unescaped quotes in strings
                $fixed = $this->fixJsonString($raw);
                $decoded = json_decode($fixed, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON parsing failed after fix attempt', [
                        'error' => json_last_error_msg(),
                        'raw_preview' => substr($raw, 0, 1000),
                    ]);
                    throw new Exception('AI generation returned invalid JSON: '.json_last_error_msg().'. Raw response preview: '.substr($raw, 0, 500));
                }
            }

            if (! is_array($decoded)) {
                throw new Exception('AI generation returned invalid JSON. Raw response: '.substr($response->text, 0, 500));
            }

            return [
                'slug' => $decoded['slug'] ?? null,
                'content' => $decoded['content'] ?? '',
                'excerpt' => $decoded['excerpt'] ?? null,
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

    private function getExtractionSystemPrompt(bool $includeAuthorSuggestion = false): string
    {
        $basePrompt = 'You are an expert at extracting film-related articles from web pages. '.
               'You analyze HTML content with structural tags (headings, paragraphs, lists) and identify articles about movies, reviews, news, and streaming. '.
               'Use the HTML structure to better understand content hierarchy and context.';

        if ($includeAuthorSuggestion) {
            $basePrompt .= ' Additionally, you match each article with the most suitable author based on their specialization, writing style, and personality.';
        }

        $basePrompt .= ' Return ONLY valid JSON without any markdown formatting, code blocks, or explanations. '.
               'If no film articles are found, return {"articles": []}.';

        return $basePrompt;
    }

    private function getExtractionPrompt(string $html, string $sourceUrl, ?array $authors = null): string
    {
        $authorsSection = '';
        $jsonFormat = '';

        if ($authors !== null && ! empty($authors)) {
            $authorsJson = json_encode($authors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $authorsSection = "\n\nAvailable Authors (for matching with articles):\n{$authorsJson}\n\nFor each article, determine which author is most suitable based on:\n- Article type (review, news, streaming guide, etc.)\n- Film genre and tone\n- Target audience\n- Author's specialization and writing style\n- Author's personality and expertise";
            $jsonFormat = <<<'JSON'
{
  "articles": [
    {
      "title": "Article Title",
      "content_summary": "First 2-3 paragraphs of content...",
      "author_name": "Author Name (if available)",
      "published_at": "2024-01-01 10:00:00",
      "image_url": "https://example.com/image.jpg (if available)",
      "article_url": "https://example.com/article-url",
      "suggested_author_id": 1
    }
  ]
}
JSON;
        } else {
            $jsonFormat = <<<'JSON'
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
JSON;
        }

        return <<<PROMPT
Analyze this HTML content from {$sourceUrl} and extract all film-related articles.
The content includes structural HTML tags (headings, paragraphs, lists) that help identify article structure.

Requirements:
- Only extract articles about movies, film reviews, news, or streaming content
- Use HTML structure (h1, h2, p, ul, li tags) to understand content hierarchy
- Skip navigation, ads, comments, and non-film content
- Extract the main article content (first 2-3 paragraphs)
- Include publication date if available{$authorsSection}
- Return ONLY valid JSON in this exact format without any additional text:

{$jsonFormat}

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
               'CRITICAL: Write COMPLETE, FULL articles. NEVER use ellipsis (...) or truncate content. Every paragraph must be fully written. '.
               'Respond ONLY with valid JSON containing: slug, content (complete HTML article), excerpt, headline, page_title, meta_description, tags (array).';
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
  "slug": "example-slug-here",
  "content": "<h2>Complete Article Title</h2><p>Full first paragraph with complete content...</p><h3>Subsection Title</h3><p>Full second paragraph with complete content...</p>",
  "excerpt": "Complete excerpt text here",
  "headline": "Complete Headline Here",
  "page_title": "Complete Page Title Here",
  "meta_description": "Complete meta description here",
  "tags": ["tag1", "tag2", "tag3"]
}

CRITICAL REQUIREMENTS:
- "content" MUST contain the COMPLETE, FULL article text. NEVER use ellipsis (...) or truncate content. Write the entire article from start to finish.
- "content" must be well-structured HTML (H2, H3 headings, paragraphs, optionally lists) with 400-600 words in Czech.
- Article structure: H2 for main sections, H3 for subsections, <p> for paragraphs, <ul>/<ol> for lists when appropriate.
- Write the COMPLETE article - every paragraph must be fully written, no placeholders, no "...", no truncation.
- "excerpt" must be 120-150 characters in Czech, engaging teaser for homepage/categories (no HTML).
- "headline" must be engaging SEO title, max 60 characters.
- "page_title" max 60 characters, suitable for title tag - engaging SEO headline.
- "meta_description" 140-160 characters, captures the article essence.
- "tags" contains 3-6 short key phrases (no #).
- Mention streaming info only if the text directly contains it.
- Do NOT use markdown, only HTML in "content".
- NEVER use ellipsis (...) or "..." in the content field - always write complete, full paragraphs.

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

    /**
     * Attempt to fix JSON string by escaping unescaped quotes in string values
     */
    private function fixJsonString(string $json): string
    {
        $result = '';
        $length = strlen($json);
        $inString = false;
        $escapeNext = false;
        $stringStart = 0;

        for ($i = 0; $i < $length; $i++) {
            $char = $json[$i];
            $nextChar = $i < $length - 1 ? $json[$i + 1] : '';

            if ($escapeNext) {
                // Previous char was backslash, this char is escaped
                $result .= $char;
                $escapeNext = false;

                continue;
            }

            if ($char === '\\') {
                // Escape character
                $escapeNext = true;
                $result .= $char;

                continue;
            }

            if ($char === '"') {
                if (! $inString) {
                    // Starting a string
                    $inString = true;
                    $stringStart = $i;
                    $result .= $char;
                } else {
                    // Check if this is the end of string or a quote inside string
                    // Look ahead to see if next non-whitespace char is : , } ] or end of line
                    $j = $i + 1;
                    while ($j < $length && in_array($json[$j], ["\n", "\r", "\t", ' '], true)) {
                        $j++;
                    }

                    if ($j >= $length || in_array($json[$j], [':', ',', '}', ']'], true)) {
                        // This is the end of string
                        $inString = false;
                        $result .= $char;
                    } else {
                        // This is an unescaped quote inside a string - escape it
                        $result .= '\\"';
                    }
                }
            } else {
                $result .= $char;
            }
        }

        return $result;
    }
}
