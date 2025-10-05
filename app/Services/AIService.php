<?php

namespace App\Services;

use Exception;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class AIService
{
    private Prism $prism;

    public function __construct()
    {
        $this->prism = Prism::builder()
            ->withProvider(Provider::OpenAI)
            ->withModel('gpt-3.5-turbo')
            ->build();
    }

    /**
     * Extract articles from HTML content using AI
     */
    public function extractArticlesFromHtml(string $html, string $sourceUrl): array
    {
        try {
            $response = $this->prism->generateText(
                systemPrompt: $this->getExtractionSystemPrompt(),
                prompt: $this->getExtractionPrompt($html, $sourceUrl)
            );

            $result = json_decode($response->text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI');
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
            // Switch to premium model for generation
            $prism = Prism::builder()
                ->withProvider(Provider::OpenAI)
                ->withModel('gpt-4-turbo')
                ->build();

            $response = $prism->generateText(
                systemPrompt: $this->getGenerationSystemPrompt($data['author']),
                prompt: $this->getGenerationPrompt($data)
            );

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
            $response = $this->prism->generateText(
                systemPrompt: 'You are a film research assistant. Provide factual information about films.',
                prompt: $this->getResearchPrompt($title, $originalContent)
            );

            $result = json_decode($response->text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }

            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    private function getExtractionSystemPrompt(): string
    {
        return 'You are an expert at extracting film-related articles from web pages. '.
               'You analyze HTML content and identify articles about movies, reviews, news, and streaming. '.
               'Return only valid JSON with structured article data.';
    }

    private function getExtractionPrompt(string $html, string $sourceUrl): string
    {
        return <<<PROMPT
Analyze this HTML content from {$sourceUrl} and extract all film-related articles.

Requirements:
- Only extract articles about movies, film reviews, news, or streaming content
- Skip navigation, ads, comments, and non-film content
- Extract the main article content (first 2-3 paragraphs)
- Include publication date if available
- Return in this exact JSON format:

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
