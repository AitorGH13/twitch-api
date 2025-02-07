<?php
class Database {
    // Esta función ahora valida el token directamente desde Twitch utilizando el OAuth Token almacenado.
    public static function validateAccessToken() {
        $url = "https://id.twitch.tv/oauth2/validate";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // El token de acceso y el Client ID de Twitch se pasan automáticamente
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . TwitchConfig::OAUTH_TOKEN
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode !== 200) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
            exit;
        }

        return json_decode($response, true);
    }
}
?>
