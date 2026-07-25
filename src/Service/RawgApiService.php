<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Cache\Cache;
use Cake\Core\Exception\CakeException;
use Cake\Http\Client;

/**
 * Thin wrapper around the RAWG.io video game database API.
 *
 * @link https://rawg.io/apidocs
 */
class RawgApiService
{
    protected const BASE_URL = 'https://api.rawg.io/api';

    protected Client $client;

    protected string $apiKey;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->apiKey = (string)env('RAWG_API_KEY', '');
    }

    /**
     * Search RAWG for games matching a query string.
     *
     * @param string $query Search term.
     * @param int $limit Max number of results.
     * @return array<int, array<string, mixed>> Normalized game data.
     */
    public function search(string $query, int $limit = 20): array
    {
        if ($this->apiKey === '') {
            throw new CakeException('RAWG_API_KEY is not configured. Set it in config/.env.');
        }

        $cacheKey = 'search_' . md5(strtolower($query) . '_' . $limit);

        return Cache::remember($cacheKey, function () use ($query, $limit) {
            $response = $this->client->get(self::BASE_URL . '/games', [
                'key' => $this->apiKey,
                'search' => $query,
                'page_size' => $limit,
            ]);

            if (!$response->isOk()) {
                throw new CakeException('RAWG API request failed with status ' . $response->getStatusCode());
            }

            $data = $response->getJson();
            $results = $data['results'] ?? [];

            return array_map([$this, 'normalize'], $results);
        }, 'rawg');
    }

    /**
     * Normalize a raw RAWG game payload into the shape the app uses.
     *
     * @param array<string, mixed> $game Raw RAWG game data.
     * @return array<string, mixed>
     */
    protected function normalize(array $game): array
    {
        $genres = array_map(fn (array $genre): string => $genre['name'], $game['genres'] ?? []);

        return [
            'rawg_id' => $game['id'],
            'title' => $game['name'],
            'cover_url' => $game['background_image'] ?? null,
            'genres' => implode(', ', $genres),
            'rating' => $game['rating'] ?? null,
            'released' => $game['released'] ?? null,
        ];
    }
}
