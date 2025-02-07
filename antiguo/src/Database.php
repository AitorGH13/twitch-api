<?php
class Database {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            $host = "localhost";
            $dbname = "twitchanalytics";
            $username = "mytwitchan6c";
            $password = "63H7S7UU";
            
            try {
                self::$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
            }
        }
        return self::$conn;
    }

    public static function validateAccessToken($token) {
        $db = self::getConnection();
        $stmt = $db->prepare("SELECT expires_at FROM tokens WHERE token = ?");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$result || (strtotime($result['expires_at']) < time())) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
            exit;
        }
    
        return true;
    }    
}
?>
