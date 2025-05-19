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
        if ($this->isTesting()) {
            // En testing, devolvemos stub para todos los ids excepto '9999'
            if ($id === '9999') {
                return [];
            }
            $lastDigit = substr($id, -1);
            return [[
                'id'               => $id,
                'login'            => "login{$id}",
                'display_name'     => "Display {$lastDigit}",
                'type'             => '',
                'broadcaster_type' => 'partner',
                'description'      => 'Test description.',
                'profile_image_url'=> 'https://example.com/profile.png',
                'offline_image_url'=> 'https://example.com/offline.png',
                'view_count'       => 1234,
                'created_at'       => '2020-01-01 00:00:00',
            ]];
        }

        $url      = "https://api.twitch.tv/helix/users?id={$id}";
        $response = callTwitchApi($url);
        return $response['data'] ?? [];
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
                ['title'=>'Title of Stream 1','user_name'=>'User1'],
                ['title'=>'Title of Stream 2','user_name'=>'User2'],
                ['title'=>'Title of Stream 3','user_name'=>'User3'],
            ];
        }
        $url      = 'https://api.twitch.tv/helix/streams';
        $response = callTwitchApi($url);
        return $response['data'] ?? [];
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

        $url      = "https://api.twitch.tv/helix/streams?first={$limit}";
        $response = callTwitchApi($url);
        return $response['data'] ?? [];
    }
}
