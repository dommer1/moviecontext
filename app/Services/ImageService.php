<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public function getImageForArticle(string $title, ?string $originalImageUrl, array $researchData): ?string
    {
        \Log::info('ImageService: Getting image for article', [
            'title' => $title,
            'has_original_image' => ! empty($originalImageUrl),
            'research_keys' => array_keys($researchData),
        ]);

        // 1. Najprv skúsiť pôvodný obrázok zo zdroja
        if ($originalImageUrl && $this->isValidImageUrl($originalImageUrl)) {
            \Log::info('ImageService: Trying original image URL', ['url' => $originalImageUrl]);
            $downloadedPath = $this->downloadImage($originalImageUrl, $title);
            if ($downloadedPath) {
                \Log::info('ImageService: Original image downloaded successfully', ['path' => $downloadedPath]);

                return $downloadedPath;
            }
            \Log::info('ImageService: Original image download failed');
        }

        // 2. Ak sa nepodarilo, skúsiť TMDB backdrop
        \Log::info('ImageService: Trying TMDB backdrop');
        $backdropUrl = $this->getTMDBBackdrop($title, $researchData);
        if ($backdropUrl) {
            \Log::info('ImageService: Found TMDB backdrop', ['url' => $backdropUrl]);
            $downloadedPath = $this->downloadImage($backdropUrl, $title);
            if ($downloadedPath) {
                \Log::info('ImageService: TMDB backdrop downloaded successfully', ['path' => $downloadedPath]);

                return $downloadedPath;
            }
            \Log::info('ImageService: TMDB backdrop download failed');
        } else {
            \Log::info('ImageService: No TMDB backdrop found');
        }

        return null;
    }

    private function isValidImageUrl(string $url): bool
    {
        // Základná validácia URL
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Skontrolovať či je to obrázok
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($extension, $validExtensions);
    }

    private function downloadImage(string $url, string $title): ?string
    {
        try {
            $response = Http::timeout(30)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $contentType = $response->header('Content-Type');
            if (! str_contains($contentType, 'image/')) {
                return null;
            }

            // Vygenerovať filename
            $slug = Str::slug($title);
            $extension = $this->getImageExtension($contentType, $url);
            $filename = $slug.'_'.time().'.'.$extension;

            // Uložiť do storage/app/public/images/
            $path = 'images/'.$filename;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Exception $e) {
            \Log::warning('Failed to download image: '.$e->getMessage(), [
                'url' => $url,
                'title' => $title,
            ]);

            return null;
        }
    }

    private function getImageExtension(string $contentType, string $url): string
    {
        // Najprv skúsiť z Content-Type
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        if (isset($extensions[$contentType])) {
            return $extensions[$contentType];
        }

        // Fallback na URL extension
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return $extension ?: 'jpg';
    }

    private function getTMDBBackdrop(string $title, array $researchData): ?string
    {
        try {
            // Pokúsiť sa nájsť film podľa názvu
            $movieId = $this->findMovieId($title, $researchData);

            if (! $movieId) {
                return null;
            }

            // Získať images pre film
            $response = Http::timeout(30)->get(config('services.tmdb.base_url')."/movie/{$movieId}/images", [
                'api_key' => config('services.tmdb.api_key'),
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            // Nájsť backdrop (nie poster)
            $backdrops = $data['backdrops'] ?? [];

            if (empty($backdrops)) {
                return null;
            }

            // Vybrať prvý backdrop (alebo najobľúbenejší)
            $backdrop = collect($backdrops)->sortByDesc('vote_count')->first();

            if (! $backdrop || ! isset($backdrop['file_path'])) {
                return null;
            }

            // Vytvoriť full URL (použijeme w1280 pre dobrú kvalitu)
            return config('services.tmdb.image_base_url').'/w1280'.$backdrop['file_path'];

        } catch (\Exception $e) {
            \Log::warning('Failed to get TMDB backdrop: '.$e->getMessage(), [
                'title' => $title,
            ]);

            return null;
        }
    }

    private function findMovieId(string $title, array $researchData): ?int
    {
        try {
            // Najprv skúsiť z research data ak máme TMDB ID
            if (isset($researchData['tmdb_id'])) {
                \Log::info('ImageService: Using TMDB ID from research data', ['tmdb_id' => $researchData['tmdb_id']]);

                return (int) $researchData['tmdb_id'];
            }

            // Skúsiť použiť searched_title ak existuje (z ResearchService)
            $searchTitle = $researchData['searched_title'] ?? $title;

            // Pokúsiť sa extrahovať názov filmu z názvu článku
            // Ak obsahuje úvodzovky, použiť obsah úvodzoviek
            if (preg_match('/"([^"]+)"/', $searchTitle, $matches)) {
                $searchTitle = $matches[1];
            }

            \Log::info('ImageService: Searching TMDB for movie', ['original_title' => $title, 'search_title' => $searchTitle]);

            // Inak hľadať podľa názvu
            $response = Http::timeout(30)->get(config('services.tmdb.base_url').'/search/movie', [
                'api_key' => config('services.tmdb.api_key'),
                'query' => $searchTitle,
                'language' => 'en-US', // Angličtina má viac výsledkov
            ]);

            if (! $response->successful()) {
                \Log::info('ImageService: TMDB search failed', ['status' => $response->status()]);

                return null;
            }

            $data = $response->json();
            $results = $data['results'] ?? [];

            \Log::info('ImageService: TMDB search results', ['count' => count($results)]);

            if (empty($results)) {
                return null;
            }

            // Vybrať prvý výsledok (alebo najpopulárnejší)
            $movie = collect($results)->sortByDesc('popularity')->first();

            \Log::info('ImageService: Selected movie from TMDB', [
                'movie_id' => $movie['id'] ?? null,
                'title' => $movie['title'] ?? null,
            ]);

            return $movie['id'] ?? null;

        } catch (\Exception $e) {
            \Log::warning('Failed to find movie in TMDB: '.$e->getMessage(), [
                'title' => $title,
            ]);

            return null;
        }
    }
}
