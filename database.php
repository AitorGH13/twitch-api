<?php
class Database {
    private static $instance = null;
    private $connection;

    // Parámetros de conexión para MySQL
    private $host = 'localhost'; // O la IP de tu servidor MySQL
    private $dbname = 'twitchanalytics'; // Nombre de tu base de datos
    private $username = 'mytwitchan6c'; // Usuario para conectar a la base de datos
    private $password = '63H7S7UU'; // Contraseña para el usuario

    private function __construct() {
        try {
            // Conexión a MySQL usando PDO
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initializeDatabase();
        } catch (PDOException $e) {
            // Manejo de errores si la conexión falla
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function initializeDatabase() {
        // Crear tablas si no existen (adaptado a MySQL)
        $this->connection->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            api_key VARCHAR(255) UNIQUE NOT NULL
        )");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            api_key VARCHAR(255) UNIQUE NOT NULL,
            token VARCHAR(255) UNIQUE NOT NULL,
            expires_at DATETIME NOT NULL
        )");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS cached_games (
            id INT AUTO_INCREMENT PRIMARY KEY,
            data TEXT NOT NULL,
            last_updated DATETIME NOT NULL
        )");
    }
}
?>
