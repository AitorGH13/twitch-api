<?php

declare(strict_types=1);

namespace App\TwitchApi;

use PDO;

class Database
{
    private static $conn;

    public function getConnection()
    {
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
