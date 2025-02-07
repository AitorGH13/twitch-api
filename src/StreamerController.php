<?php
require_once "Database.php";

class StreamerController {
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

    public static function getStreamerById($id) {
        $url = "https://api.twitch.tv/helix/users?id=$id"; // Endpoint para obtener datos del streamer
        $response = self::callTwitchApi($url);

        if (empty($response['data'])) {
            http_response_code(404);
            return ["error" => "User not found."];
        }

        return $response['data'][0];
    }
}
?>
