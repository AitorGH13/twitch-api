<?php

declare(strict_types=1);

namespace App\TwitchApi;

use App\TwitchApi\AuthController;
use App\TwitchApi\Database;
use PDO;

class StreamerController
{
    private static function callTwitchApi(string $url): array
    {
        $oauthToken = 'at4xi9qrfxqbvlp5d0mqt6g7z5tzzv';
        $clientId   = 'pl90uakzou662frdn51bgohgalbxj5';

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

    public static function getStreamerById(string $idStreamer, string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Token is invalid or expired.'];
        }

        if ($idStreamer === '') {
            http_response_code(400);
            return ['error' => "Invalid or missing 'id' parameter."];
        }

        $dbService = new Database();
        $dbConnection = $dbService->getConnection();
        $sqlSelect    = <<<'SQL'
SELECT id, login, display_name, type, broadcaster_type, description,
       profile_image_url, offline_image_url, view_count, created_at
FROM users
WHERE id = :id
SQL;
        $stmtSelect = $dbConnection->prepare($sqlSelect);
        $stmtSelect->execute([':id' => $idStreamer]);
        $result = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($result !== false) {
            return $result;
        }

        $url         = "https://api.twitch.tv/helix/users?id=$idStreamer";
        $apiResponse = self::callTwitchApi($url);
        $data        = $apiResponse['data'] ?? [];

        if (empty($data)) {
            http_response_code(404);
            return ['error' => 'User not found.'];
        }

        $user = $data[0];
        $sqlInsert = <<<'SQL'
INSERT INTO users
    (id, login, display_name, type, broadcaster_type, description,
     profile_image_url, offline_image_url, view_count, created_at)
VALUES
    (:id, :login, :display_name, :type, :broadcaster_type, :description,
     :profile_image_url, :offline_image_url, :view_count, :created_at)
SQL;
        $stmtInsert = $dbConnection->prepare($sqlInsert);
        $stmtInsert->execute([
            ':id'               => $user['id'],
            ':login'            => $user['login'],
            ':display_name'     => $user['display_name'],
            ':type'             => $user['type'],
            ':broadcaster_type' => $user['broadcaster_type'],
            ':description'      => $user['description'],
            ':profile_image_url' => $user['profile_image_url'],
            ':offline_image_url' => $user['offline_image_url'],
            ':view_count'       => $user['view_count'],
            ':created_at'       => $user['created_at'],
        ]);

        return $user;
    }
}
