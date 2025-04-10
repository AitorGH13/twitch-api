<?php
class Database {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            $host = "localhost";
            $dbname = "twitchanalytics";
            $username = "root";
            $password = "dxl2025*";
            
            try {
                self::$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
            }
        }
        return self::$conn;
    }
}
?>
