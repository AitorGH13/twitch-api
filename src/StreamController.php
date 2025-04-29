<?php

declare(strict_types=1);

namespace App\TwitchApi;

use App\TwitchApi\AuthController;
use App\TwitchApi\TwitchAuth;

class StreamController
{
    public static function getLiveStreams(string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Twitch access token is invalid or has expired.'];
        }

        $url      = 'https://api.twitch.tv/helix/streams';
        $response = callTwitchApi($url);

        return array_map(function ($stream) {
            return [
                'title'     => $stream['title'],
                'user_name' => $stream['user_name']
            ];
        }, $response['data']);
    }

    public static function getTopEnrichedStreams(int $limit, string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Twitch access token is invalid or has expired.'];
        }
        if ($limit <= 0) {
            http_response_code(400);
            return ['error' => "Invalid 'limit' parameter."];
        }

        $url      = "https://api.twitch.tv/helix/streams?first=$limit";
        $response = callTwitchApi($url);
        $streams = $response['data'];

        $enrichedStreams = [];

        foreach ($streams as $stream) {
            $userId       = $stream['user_id'];
            $userUrl      = "https://api.twitch.tv/helix/users?id=$userId";
            $userResponse = callTwitchApi($userUrl);
            $user         = $userResponse['data'][0];

            $enrichedStreams[] = [
                'stream_id'             => $stream['id'],
                'user_id'               => $userId,
                'user_name'             => $stream['user_name'],
                'viewer_count'          => $stream['viewer_count'],
                'title'                 => $stream['title'],
                'user_display_name'     => $user['display_name'],
                'profile_image_url'     => $user['profile_image_url'],
            ];
        }

        return $enrichedStreams;
    }
}
