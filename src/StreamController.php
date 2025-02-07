<?php
require_once "Database.php";

class StreamController {
    private static function callTwitchApi($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Usamos el OAuth Token y Client ID desde TwitchConfig
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . TwitchConfig::OAUTH_TOKEN,
            "Client-Id: " . TwitchConfig::CLIENT_ID
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public static function getLiveStreams() {
        $url = "https://api.twitch.tv/helix/streams";
        $response = self::callTwitchApi($url);
        return $response['data']; // Devuelvo los streams en vivo
    }

    public static function getTopEnrichedStreams($limit) {
        $url = "https://api.twitch.tv/helix/streams?first=$limit"; // Obtener los primeros 'limit' streams
        $response = self::callTwitchApi($url);
        
        $enrichedStreams = [];
        foreach ($response['data'] as $stream) {
            $userId = $stream['user_id'];
            $userUrl = "https://api.twitch.tv/helix/users?id=$userId";
            $userResponse = self::callTwitchApi($userUrl);
            $user = $userResponse['data'][0];

            $enrichedStreams[] = [
                'stream_id' => $stream['id'],
                'title' => $stream['title'],
                'viewer_count' => $stream['viewer_count'],
                'display_name' => $user['display_name'],
                'profile_image_url' => $user['profile_image_url']
            ];
        }

        return $enrichedStreams;
    }
}
?>
