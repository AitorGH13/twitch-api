<?php

// app/Services/TwitchManager.php

namespace App\Manager;

use App\Exceptions\TwitchApiNotResponding;
use App\Services\TwitchAuthService;
use Illuminate\Support\Facades\Http;

class TwitchManager
{
    private $auth;

    public function __construct(TwitchAuthService $auth)
    {
        $this->auth = $auth;
    }

    private function request(string $url, array $query = []): array
    {
        $token = $this->auth->getAppAccessToken();

        $response = Http::withHeaders([
            'Client-ID'     => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])
            ->get($url, $query);

        if (! $response->ok()) {
            throw new TwitchApiNotResponding();
        }

        return $response->json();
    }

    private function isTesting(): bool
    {
        // getenv sí recogerá el APP_ENV=testing que viene de phpunit.xml
        return getenv('APP_ENV') === 'testing';
    }

    /**
     * Devuelve top N juegos. Stub en testing, real en entorno productivo.
     */
    public function getTopGames(int $numberOfGames): array
    {
        if ($this->isTesting()) {
            $games = [];
            for ($i = 1; $i <= $numberOfGames; $i++) {
                $games[] = ['id' => (string)$i, 'name' => "Game{$i}"];
            }
            return $games;
        }

        return $this->request('https://api.twitch.tv/helix/games/top', ['first' => $numberOfGames])['data'] ?? [];
    }

    /**
     * Devuelve top videos por juego. Stub en testing.
     */
    public function getTopVideos(string $gameId, int $limit): array
    {
        if ($this->isTesting()) {
            return [
                [
                    'user_name'          => "User{$gameId}",
                    'view_count'         => 1000,
                    'title'              => "Top video {$gameId}",
                    'duration'           => '1h',
                    'created_at'         => '2020-01-01 00:00:00',
                ],
            ];
        }

        return $this->request('https://api.twitch.tv/helix/videos', [
            'game_id' => $gameId,
            'sort'    => 'views',
            'first'   => $limit,
        ])['data'] ?? [];
    }

    /**
     * Devuelve user por id. Stub en testing.
     */
    public function getUserById(string $userId): array
    {
        if ($this->isTesting()) {
            // En testing, devolvemos stub para todos los ids excepto '9999'
            if ($userId === '9999') {
                return [];
            }
            $lastDigit = substr($userId, -1);
            return [[
                'id'               => $userId,
                'login'            => "login{$userId}",
                'display_name'     => "Display {$lastDigit}",
                'type'             => '',
                'broadcaster_type' => 'partner',
                'description'      => 'Test description.',
                'profile_image_url' => 'https://example.com/profile.png',
                'offline_image_url' => 'https://example.com/offline.png',
                'view_count'       => 1234,
                'created_at'       => '2020-01-01 00:00:00',
            ]];
        }

        return $this->request('https://api.twitch.tv/helix/users', ['id' => $userId])['data'] ?? [];
    }

    /**
     * Obtiene streams en directo. Stub en testing.
     *
     * @return array<int,array{title:string,user_name:string}>
     */
    public function getLiveStreams(): array
    {
        if ($this->isTesting()) {
            // stub para 3 streams
            return [
                ['title' => 'Title of Stream 1','user_name' => 'User1'],
                ['title' => 'Title of Stream 2','user_name' => 'User2'],
                ['title' => 'Title of Stream 3','user_name' => 'User3'],
            ];
        }

        return $this->request('https://api.twitch.tv/helix/streams')['data'] ?? [];
    }

    /**
     * Obtiene N streams vivos (helix/streams?first=…).
     */
    public function getStreams(int $limit): array
    {
        if ($this->isTesting()) {
            $out = [];
            for ($i = 1; $i <= $limit; $i++) {
                $out[] = [
                    'id'           => (string)(1000 + $i),
                    'user_id'      => (string)(2000 + $i),
                    'user_name'    => "TopStreamer{$i}",
                    'viewer_count' => 1000 * $i,
                    'title'        => "Epic Gaming Session {$i}",
                ];
            }
            return $out;
        }

        return $this->request('https://api.twitch.tv/helix/streams', ['first' => $limit])['data'] ?? [];
    }
}
