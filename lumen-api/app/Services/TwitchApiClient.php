<?php
// app/Services/TwitchApiClient.php
namespace App\Services;

class TwitchApiClient
{
    private function isTesting(): bool
    {
        // getenv sí recogerá el APP_ENV=testing que viene de phpunit.xml
        return getenv('APP_ENV') === 'testing';
    }

    /**
     * Devuelve top N juegos. Stub en testing, real en entorno productivo.
     */
    public function getTopGames(int $n): array
    {
        if ($this->isTesting()) {
            $games = [];
            for ($i = 1; $i <= $n; $i++) {
                $games[] = ['id' => (string)$i, 'name' => "Game{$i}"];
            }
            return $games;
        }

        $url      = "https://api.twitch.tv/helix/games/top?first={$n}";
        $response = callTwitchApi($url);
        return $response['data'] ?? [];
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

        $url      = "https://api.twitch.tv/helix/videos?game_id={$gameId}&sort=views&first={$limit}";
        $response = callTwitchApi($url);
        return $response['data'] ?? [];
    }
}

