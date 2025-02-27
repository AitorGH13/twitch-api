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

    public static function getStreamerById($id, $token) {
        if (!AuthController::validateAccessToken($token)) {
            http_response_code(401);
            return ["error" => "Unauthorized. Token is invalid or expired."];
        } else if (!$id) {
            http_response_code(400);
            return ["error" => "Invalid or missing 'id' parameter."];
        }

        $db = database::getConnection();
        $stmt = $db->prepare("SELECT id, login, display_name, type, broadcaster_type, description, profile_image_url, offline_image_url, view_count, created_at FROM users where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $url = "https://api.twitch.tv/helix/users?id=$id";
            $response = self::callTwitchApi($url);

            if (empty($response['data'])) {
                http_response_code(404);
                return ["error" => "User not found."];
            }

            $user = $response['data'];
            $stmt = $db->prepare("INSERT INTO users (id, login, display_name, type, broadcaster_type, description, profile_image_url, offline_image_url, view_count, created_at) VALUES (:id, :login, :display_name, :type, :broadcaster_type, :description, :profile_image_url, :offline_image_url, :view_count, :created_at)");
            $stmt->execute([':id' => $user[0]['id'], ':login' => $user[0]['login'], ':display_name' => $user[0]['login'], ':type' => $user[0]['type'], ':broadcaster_type' => $user[0]['broadcaster_type'], ':description' => $user[0]['description'], ':profile_image_url' => $user[0]['profile_image_url'], ':offline_image_url' => $user[0]['offline_image_url'], ':view_count' => $user[0]['view_count'], ':created_at' => $user[0]['created_at']]);
            return $user[0];
        } else {
            return $result;
        }
    }
}
?>
