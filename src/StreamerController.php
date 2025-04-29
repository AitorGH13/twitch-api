<?php

declare(strict_types=1);

namespace App\TwitchApi;

use App\TwitchApi\AuthController;
use App\TwitchApi\Database;
use App\TwitchApi\TwitchAuth;
use PDO;

class StreamerController
{
    public static function getStreamerById(string $idStreamer, string $token): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Twitch access token is invalid or has expired.'];
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
        $apiResponse = callTwitchApi($url);
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
