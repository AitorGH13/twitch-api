<?php

declare(strict_types=1);

namespace App\TwitchApi;

use App\TwitchApi\AuthController;

class StreamController
{
    private static function callTwitchApi(string $url): array
    {
        $oauthToken = 'n2rnsruj57culzwz2iznqx6y5jbata';
        $clientId   = 'iw4dxrhn2yqaethe9b6uwdbanf3xiw';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/../cacert.pem');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $oauthToken",
            "Client-Id: $clientId",
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            http_response_code(500);
            return ['error' => 'Internal server error.'];
        }

        curl_close($curl);

        return json_decode($response, true);
    }

    public static function getLiveStreams(string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Token is invalid or expired.'];
        }

        $url      = 'https://api.twitch.tv/helix/streams';
        $response = self::callTwitchApi($url);

        return $response['data'];
    }

    public static function getTopEnrichedStreams(int $limit, string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Token is invalid or expired.'];
        }

        $url      = "https://api.twitch.tv/helix/streams?first=$limit";
        $response = self::callTwitchApi($url);

        $enrichedStreams = [];

        foreach ($response['data'] as $stream) {
            $userId       = $stream['user_id'];
            $userUrl      = "https://api.twitch.tv/helix/users?id=$userId";
            $userResponse = self::callTwitchApi($userUrl);
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
