<?php
require_once "Database.php";

class StreamController {
    public static function getLiveStreams() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT title, user_id FROM streams");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTopEnrichedStreams($limit) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.id as stream_id, s.title, s.viewer_count, u.display_name, u.profile_image_url 
            FROM streams s 
            JOIN streamers u ON s.user_id = u.id 
            ORDER BY s.viewer_count DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
