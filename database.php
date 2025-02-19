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
}
?>