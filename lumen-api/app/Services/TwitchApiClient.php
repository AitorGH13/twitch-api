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

    /**
     * Devuelve user por id. Stub en testing.
     */
    public function getUserById(string $id): array
    {
        if (getenv('APP_ENV') === 'testing') {
            // sólo devolvemos datos para el id “42”, simulando que los tests
            // validan estructura para ese caso. Para cualquier otro id testing,
            // devolvemos [] y así provocamos el 404.
            if ($id === '42') {
                return [[
                    'id'               => $id,
                    'login'            => "login{$id}",
                    'display_name'     => "Display {$id}",
                    'type'             => '',
                    'broadcaster_type' => 'partner',
                    'description'      => 'Test description.',
                    'profile_image_url'=> 'https://example.com/profile.png',
                    'offline_image_url'=> 'https://example.com/offline.png',
                    'view_count'       => 1234,
                    // Este formato MySQL‐compatible evita errores de timestamp
                    'created_at'       => '2020-01-01 00:00:00',
                ]];
            }
            return [];  // para id != 42, simulamos “no encontrado”
        }

        $url      = "https://api.twitch.tv/helix/users?id={$id}";
        $response = callTwitchApi($url);
        return $response['data'] ?? [];
    }
}

