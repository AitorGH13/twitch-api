<?php

namespace App\Clients;

use App\Exceptions\TwitchApiException;
use App\Interfaces\TwitchClientInterface;
use App\Services\TwitchAuthService;
use Illuminate\Support\Facades\Http;

final class HttpTwitchClient implements TwitchClientInterface
{
    public function __construct(private readonly TwitchAuthService $auth)
    {
    }

    public function getTopGames(int $first): array
    {
        return $this->request('https://api.twitch.tv/helix/games/top', [
            'first' => $first,
        ])['data'] ?? [];
    }

    public function getTopVideos(string $gameId, int $first): array
    {
        return $this->request('https://api.twitch.tv/helix/videos', [
            'game_id' => $gameId,
            'sort'    => 'views',
            'first'   => $first,
        ])['data'] ?? [];
    }

    public function getUserById(string $userId): array
    {
        return $this->request('https://api.twitch.tv/helix/users', [
            'id' => $userId,
        ])['data'] ?? [];
    }

    public function getLiveStreams(int $first): array
    {
        return $this->request('https://api.twitch.tv/helix/streams', [
            'first' => $first,
        ])['data'] ?? [];
    }

    public function getStreams(int $first): array
    {
        return $this->request('https://api.twitch.tv/helix/streams', [
            'first' => $first,
        ])['data'] ?? [];
    }

    private function request(string $url, array $query = []): array
    {
        $token = $this->auth->getAppAccessToken();

        $response = Http::withHeaders([
            'Client-ID'     => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])->get($url, $query);

        if (! $response->ok()) {
            throw new TwitchApiException('Twitch API error: ' . $response->status());
        }

        return $response->json();
    }
}
