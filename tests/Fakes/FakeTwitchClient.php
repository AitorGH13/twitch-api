<?php

namespace Fakes;

use App\Interfaces\TwitchClientInterface;

class FakeTwitchClient implements TwitchClientInterface
{
    public function getTopGames(int $first): array
    {
        $out = [];
        for ($i = 1; $i <= $first; $i++) {
            $out[] = ['id' => (string)$i, 'name' => "Game$i"];
        }
        return $out;
    }

    public function getTopVideos(string $gameId, int $first): array
    {
        return [[
            'user_name'  => "User$gameId",
            'view_count' => 1000,
            'title'      => "Top video $gameId",
            'duration'   => '1h',
            'created_at' => '2020-01-01 00:00:00',
        ]];
    }

    public function getUserById(string $userId): array
    {
        if ($userId === '9999') {
            return [];
        }
        $lastDigit = substr($userId, -1);
        return [[
            'id'               => $userId,
            'login'            => "login$userId",
            'display_name'     => "Display $lastDigit",
            'type'             => '',
            'broadcaster_type' => 'partner',
            'description'      => 'Test description.',
            'profile_image_url' => 'https://example.com/profile.png',
            'offline_image_url' => 'https://example.com/offline.png',
            'view_count'       => 1234,
            'created_at'       => '2020-01-01 00:00:00',
        ]];
    }

    public function getLiveStreams(int $first): array
    {
        return [
            ['title' => 'Title of Stream 1','user_name' => 'User1'],
            ['title' => 'Title of Stream 2','user_name' => 'User2'],
            ['title' => 'Title of Stream 3','user_name' => 'User3'],
        ];
    }

    public function getStreams(int $first): array
    {
        $out = [];
        for ($i = 1; $i <= $first; $i++) {
            $out[] = [
                'id'           => (string)(1000 + $i),
                'user_id'      => (string)(2000 + $i),
                'user_name'    => "TopStreamer$i",
                'viewer_count' => 1000 * $i,
                'title'        => "Epic Gaming Session $i",
            ];
        }
        return $out;
    }
}
