<?php
require_once "Database.php";

class StreamerController {
    public static function getStreamerById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM streamers WHERE id = ?");
        $stmt->execute([$id]);
        $streamer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$streamer) {
            http_response_code(404);
            return ["error" => "User not found."];
        }
        return $streamer;
    }
}
?>
