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
            return ['error' => 'Unauthorized. Token is invalid or expired.'];
        }

        $url      = 'https://api.twitch.tv/helix/streams';
        $response = callTwitchApi($url);

        return $response['data'];
    }

    public static function getTopEnrichedStreams(int $limit, string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Token is invalid or expired.'];
        }
        if ($limit <= 0) {
            http_response_code(400);
            return ['error' => "Invalid 'limit' parameter."];
        }

        $url      = "https://api.twitch.tv/helix/streams?first=$limit";
        $response = callTwitchApi($url);

        $enrichedStreams = [];

        foreach ($response['data'] as $stream) {
            $userId       = $stream['user_id'];
            $userUrl      = "https://api.twitch.tv/helix/users?id=$userId";
            $userResponse = callTwitchApi($userUrl);
            $user         = $userResponse['data'][0];

            $enrichedStreams[] = [
                'stream_id'         => $stream['id'],
                'title'             => $stream['title'],
                'viewer_count'      => $stream['viewer_count'],
                'display_name'      => $user['display_name'],
                'profile_image_url' => $user['profile_image_url'],
            ];
        }

        return $enrichedStreams;
    }
}
