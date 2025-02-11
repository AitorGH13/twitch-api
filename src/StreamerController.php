<?php
class StreamerController {
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

    public static function getStreamerById($id) {
        $url = "https://api.twitch.tv/helix/users?id=$id";
        $response = self::callTwitchApi($url);

        if (empty($response['data'])) {
            http_response_code(404);
            return ["error" => "User not found."];
        }

        return $response['data'];
    }
}
?>
