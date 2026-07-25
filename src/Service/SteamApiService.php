<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Exception\CakeException;
use Cake\Http\Client;

/**
 * Thin wrapper around the Steam Web API, used to read a user's public
 * game library for import.
 *
 * @link https://steamcommunity.com/dev
 */
class SteamApiService
{
    protected const BASE_URL = 'https://api.steampowered.com';

    protected Client $client;

    protected string $apiKey;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->apiKey = (string)env('STEAM_API_KEY', '');
    }

    /**
     * Accepts a SteamID64, a full profile URL, or a vanity name and
     * returns a resolved SteamID64.
     *
     * @param string $input User-supplied profile identifier.
     * @return string|null
     */
    public function resolveSteamId(string $input): ?string
    {
        $input = trim($input);

        if (preg_match('/^\d{17}$/', $input)) {
            return $input;
        }

        if (preg_match('#steamcommunity\.com/profiles/(\d{17})#i', $input, $matches)) {
            return $matches[1];
        }

        $vanity = $input;
        if (preg_match('#steamcommunity\.com/id/([^/]+)#i', $input, $matches)) {
            $vanity = $matches[1];
        }

        return $this->resolveVanityUrl($vanity);
    }

    /**
     * Resolve a Steam vanity name (steamcommunity.com/id/{vanity}) to a SteamID64.
     *
     * @param string $vanity Vanity name.
     * @return string|null
     */
    public function resolveVanityUrl(string $vanity): ?string
    {
        $this->assertApiKey();

        $response = $this->client->get(self::BASE_URL . '/ISteamUser/ResolveVanityURL/v0001/', [
            'key' => $this->apiKey,
            'vanityurl' => $vanity,
            'format' => 'json',
        ]);

        if (!$response->isOk()) {
            throw new CakeException('Steam API request failed with status ' . $response->getStatusCode());
        }

        $data = $response->getJson();
        $result = $data['response'] ?? [];

        if (($result['success'] ?? null) !== 1) {
            return null;
        }

        return $result['steamid'] ?? null;
    }

    /**
     * Fetch a user's owned games. Requires the Steam profile's game
     * details to be public.
     *
     * @param string $steamId64 Resolved SteamID64.
     * @return array<int, array<string, mixed>> Normalized owned games.
     */
    public function getOwnedGames(string $steamId64): array
    {
        $this->assertApiKey();

        $response = $this->client->get(self::BASE_URL . '/IPlayerService/GetOwnedGames/v0001/', [
            'key' => $this->apiKey,
            'steamid' => $steamId64,
            'format' => 'json',
            'include_appinfo' => 1,
            'include_played_free_games' => 1,
        ]);

        if (!$response->isOk()) {
            throw new CakeException('Steam API request failed with status ' . $response->getStatusCode());
        }

        $data = $response->getJson();
        $games = $data['response']['games'] ?? null;

        if ($games === null) {
            throw new CakeException(
                'Could not read this Steam library. Make sure the profile and game details are set to public.',
            );
        }

        return array_map([$this, 'normalize'], $games);
    }

    /**
     * Normalize a raw Steam owned-game payload.
     *
     * @param array<string, mixed> $game Raw Steam game data.
     * @return array<string, mixed>
     */
    protected function normalize(array $game): array
    {
        return [
            'appid' => $game['appid'],
            'name' => $game['name'],
            'playtime_minutes' => $game['playtime_forever'] ?? 0,
            'header_image' => sprintf(
                'https://cdn.akamai.steamstatic.com/steam/apps/%d/header.jpg',
                $game['appid'],
            ),
        ];
    }

    protected function assertApiKey(): void
    {
        if ($this->apiKey === '') {
            throw new CakeException('STEAM_API_KEY is not configured. Set it in config/.env.');
        }
    }
}
