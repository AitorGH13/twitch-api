<?php

declare(strict_types=1);

namespace App\TwitchApi;

use App\TwitchApi\AuthController;
use App\TwitchApi\Database;
use PDO;
use DateTime;

class VideoController
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

    private static function getTopThreeGames(): array
    {
        $url = 'https://api.twitch.tv/helix/games/top?first=3';
        $response = self::callTwitchApi($url);

        if (empty($response['data'])) {
            http_response_code(404);
            return ['error' => 'No games found.'];
        }

        return $response['data'];
    }

    private static function getTopVideos(string $gameId): array
    {
        $url = "https://api.twitch.tv/helix/videos?game_id=$gameId&sort=views&first=40";
        $response = self::callTwitchApi($url);

        if (empty($response['data'])) {
            http_response_code(404);
            return ['error' => 'No videos found.'];
        }

        return $response['data'];
    }

    public static function getTopsOfTheTops(string $token, ?int $since): array
    {
        $auth = new AuthController();
        if (! $auth->validateAccessToken($token)) {
            http_response_code(401);
            return ['error' => 'Unauthorized. Token is invalid or expired.'];
        }

        $dbService = new \App\TwitchApi\Database();
        $dbConnection = $dbService->getConnection();
        $stmtCheck    = $dbConnection->prepare('SELECT expires_at FROM topsofthetops LIMIT 1');
        $stmtCheck->execute();
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$result || strtotime($result['expires_at']) < time() || $since !== null) {
            $dbConnection->exec('DELETE FROM topsofthetops');

            if ($since === null) {
                $since = 600;
            }

            $expiresAt = (new DateTime('+' . $since . ' seconds'))->format('Y-m-d H:i:s');
            $games     = self::getTopThreeGames();
            $response  = [];

            foreach ($games as $index => $game) {
                $data      = self::getTopVideos($game['id']);
                $userName  = $data[0]['user_name'];
                $userVideos = array_filter($data, fn ($video) => $video['user_name'] === $userName);

                $response[] = [
                    'game_id'               => $game['id'],
                    'game_name'             => $game['name'],
                    'user_name'             => $userName,
                    'total_videos'          => count($userVideos),
                    'total_views'           => array_sum(array_column($userVideos, 'view_count')),
                    'most_viewed_title'     => $data[0]['title'],
                    'most_viewed_views'     => $data[0]['view_count'],
                    'most_viewed_duration'  => $data[0]['duration'],
                    'most_viewed_created_at' => $data[0]['created_at'],
                ];

                $stmtInsert = $dbConnection->prepare(<<<SQL
INSERT INTO topsofthetops (
    game_id, game_name, user_name, total_videos, total_views,
    mv_title, mv_views, mv_duration, mv_created_at, expires_at
) VALUES (
    :game_id, :game_name, :user_name, :total_videos, :total_views,
    :mv_title, :mv_views, :mv_duration, :mv_created_at, :expires_at
)
SQL);
                $stmtInsert->execute([
                    ':game_id'       => $game['id'],
                    ':game_name'     => $game['name'],
                    ':user_name'     => $userName,
                    ':total_videos'  => count($userVideos),
                    ':total_views'   => array_sum(array_column($userVideos, 'view_count')),
                    ':mv_title'      => $data[0]['title'],
                    ':mv_views'      => $data[0]['view_count'],
                    ':mv_duration'   => $data[0]['duration'],
                    ':mv_created_at' => $data[0]['created_at'],
                    ':expires_at'    => $expiresAt,
                ]);
            }

            return $response;
        }

        $stmtRead = $dbConnection->prepare(
            'SELECT game_id, game_name, user_name, total_videos, total_views,
                    mv_title AS most_viewed_title, mv_views AS most_viewed_views,
                    mv_duration AS most_viewed_duration, mv_created_at AS most_viewed_created_at
             FROM topsofthetops'
        );
        $stmtRead->execute();
        return $stmtRead->fetchAll(PDO::FETCH_ASSOC);
    }
}
