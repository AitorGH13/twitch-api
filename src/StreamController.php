<?php
require_once 'AuthController.php';
class StreamController {
    private static function callTwitchApi($url) {
        $oauthToken = "n2rnsruj57culzwz2iznqx6y5jbata";
        $clientId = "iw4dxrhn2yqaethe9b6uwdbanf3xiw";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $oauthToken,
            "Client-Id: " . $clientId
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            http_response_code(500);
            return ["error" => "Internal server error."];
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public static function getLiveStreams($token) {
        if (!AuthController::validateAccessToken($token)) {
            http_response_code(401);
            return ["error" => "Unauthorized. Token is invalid or expired."];
        }
        $url = "https://api.twitch.tv/helix/streams";
        $response = self::callTwitchApi($url);
        return $response['data'];
    }

    public static function getTopEnrichedStreams($limit, $token) {
        if (!AuthController::validateAccessToken($token)) {
            http_response_code(401);
            return ["error" => "Unauthorized. Token is invalid or expired."];
        }
        
        $url = "https://api.twitch.tv/helix/streams?first=$limit";
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
