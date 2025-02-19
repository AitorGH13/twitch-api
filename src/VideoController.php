<?php
require_once 'AuthController.php';
class VideoController {
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

    private static function getTopThreeGames() {
        $url = "https://api.twitch.tv/helix/games/top?first=3";
        $response = self::callTwitchApi($url);

        if (empty($response['data'])) {
            http_response_code(404);
            return ["error" => "User not found."];
        }

        return $response['data'];
    }

    private static function getTopVideos($gameId) {
        $url = "https://api.twitch.tv/helix/videos?game_id=" . $gameId ."&sort=views&first=40";
        $response = self::callTwitchApi($url);

        if (empty($response['data'])) {
            http_response_code(404);
            return ["error" => "User not found."];
        }

        return $response['data'];
    }

    public static function getTopsOfTheTops($token, $since) {
        if (!AuthController::validateAccessToken($token)) {
            http_response_code(401);
            return ["error" => "Unauthorized. Token is invalid or expired."];
        }

        $db = database::getConnection();
        $stmt = $db->prepare("SELECT expires_at FROM topsofthetops LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || (strtotime($result['expires_at']) < time()) || $since != null) {
            $db->exec("DELETE FROM topsofthetops");
            if ($since == null) $since = 600;
            $expiresAt = date('Y-m-d H:i:s', strtotime('+' . $since . 'seconds'));
            $games = self::getTopThreeGames();
            $response = [];
            $index = 0;
            foreach ($games as $game) {
                $data = self::getTopVideos($game['id']);
                $user_name = $data[0]['user_name'];
                $videosFirstUser = array_filter($data, function ($video) use ($user_name) {
                    return $video['user_name'] == $user_name;
                });
                $totalVideos = count($videosFirstUser);
                $totalViews = array_sum(array_column($videosFirstUser, 'view_count'));
                $response[$index] = [
                    "game_id" => $game['id'],
                    "game_name" => $game['name'],
                    "user_name" => $user_name,
                    "total_videos" => $totalVideos,
                    "total_views" => $totalViews,
                    "most_viewed_title" => $data[0]['title'],
                    "most_viewed_views" => $data[0]['view_count'],
                    "most_viewed_duration" => $data[0]['duration'],
                    "most_viewed_created_at" => $data[0]['created_at']
                ];
                $index++;
                $stmt = $db->prepare("INSERT INTO topsofthetops (game_id, game_name, user_name, total_videos, total_views, mv_title, mv_views, mv_duration, mv_created_at, expires_at) VALUES (:game_id, :game_name, :user_name, :total_videos, :total_views, :mv_title, :mv_views, :mv_duration, :mv_created_at, :expires_at)");
                $stmt->execute([':game_id' => $game['id'], ':game_name' => $game['name'], ':user_name' => $user_name, ':total_videos' => $totalVideos, ':total_views' => $totalViews, ':mv_title' => $data[0]['title'], ':mv_views' => $data[0]['view_count'], ':mv_duration' => $data[0]['duration'], ':mv_created_at' => $data[0]['created_at'], ':expires_at' => $expiresAt]);
            }
            $finalResponse = [$response[0], $response[1], $response[2]];

            return $finalResponse;
        } else {
            $stmt = $db->prepare("SELECT game_id, game_name, user_name, total_videos, total_views, mv_title AS most_viewes_title, mv_views AS most_viewed_views, mv_duration AS most_viewed_duration, mv_created_at AS most_viewed_created_at FROM topsofthetops");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    }
}
?>
