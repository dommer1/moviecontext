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
                throw new Exception('Invalid JSON response from AI: ' . json_last_error_msg() . '. Raw response: ' . substr($response->text, 0, 500));
            }

            return $result['articles'] ?? [];
        } catch (Exception $e) {
            throw new Exception('AI extraction failed: '.$e->getMessage());
        }
    }

    /**
     * Generate article content using AI
     */
    public function generateArticle(array $data): string
    {
        try {
            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4-turbo')
                ->withSystemPrompt($this->getGenerationSystemPrompt($data['author']))
                ->withPrompt($this->getGenerationPrompt($data))
                ->generate();

            return $response->text;
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
        // Remove script and style tags
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);

        // Extract text content
        $text = strip_tags($html);

        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Limit to reasonable size (around 10,000 characters should fit in gpt-3.5-turbo context)
        return substr(trim($text), 0, 10000);
    }

    private function getExtractionSystemPrompt(): string
    {
        return 'You are an expert at extracting film-related articles from web pages. '.
               'You analyze text content and identify articles about movies, reviews, news, and streaming. '.
               'Return ONLY valid JSON without any markdown formatting, code blocks, or explanations. '.
               'If no film articles are found, return {"articles": []}.';
    }

    private function getExtractionPrompt(string $html, string $sourceUrl): string
    {
        return <<<PROMPT
Analyze this text content from {$sourceUrl} and extract all film-related articles.

Requirements:
- Only extract articles about movies, film reviews, news, or streaming content
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
               'Always include Czech film context and streaming availability.';
    }

    private function getGenerationPrompt(array $data): string
    {
        $research = $data['research'] ?? [];
        $scrapedArticle = $data['scraped_article'];

        $title = $scrapedArticle['title'] ?? '';
        $summary = $scrapedArticle['content_summary'] ?? '';
        $authorName = $data['author']['name'] ?? '';
        $imdbId = $research['imdb_id'] ?? 'Unknown';
        $genres = $research['genres'] ?? 'Unknown';
        $czechRelease = $research['czech_release'] ?? 'Unknown';
        $streaming = $research['streaming'] ?? 'Unknown';
        $funFact = $research['fun_fact'] ?? 'Unknown';

        return <<<PROMPT
Write a comprehensive film article in Czech based on the following information:

Original Article:
Title: {$title}
Summary: {$summary}

Research Data:
IMDB ID: {$imdbId}
Genres: {$genres}
Czech Release: {$czechRelease}
Streaming: {$streaming}
Fun Fact: {$funFact}

Requirements:
- Write 800-1200 words in Czech
- Include your personal perspective as {$authorName}
- Add Czech film context (where to watch, local release dates)
- Include the fun fact in a "Zajímavost" section
- Mention streaming platforms available in Czech Republic
- End with personal rating/recommendation
- Write naturally, not like AI-generated content

Article Structure:
1. Introduction with your take on the film
2. Main analysis (plot, acting, direction, cinematography)
3. Czech context and availability
4. Personal conclusion
5. "Zajímavost" section with fun fact
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
