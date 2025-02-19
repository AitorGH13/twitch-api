<?php
require_once 'database.php';
class AuthController {
    public static function registerEmail($email) {
        $db = database::getConnection();
        $apiKey = bin2hex(random_bytes(8));
        $stmt = $db->prepare("INSERT INTO tokens (email, api_key) VALUES (:email, :apiKey) ON DUPLICATE KEY UPDATE api_key = :apiKey");
        $stmt->execute([':email' => $email, ':apiKey' => $apiKey]);
        return ["api_key" => $apiKey];
    }

    public static function createAccessToken($email, $apiKey) {
        $db = database::getConnection();
        $stmt = $db->prepare("SELECT expires_at FROM tokens WHERE email = :email AND api_key = :apiKey LIMIT 1");
        $stmt->execute([':email' => $email, ':apiKey' => $apiKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$result) {
            http_response_code(401);
            return ["error" => "Unauthorized. API access token is invalid."];
        } else {
            $token = bin2hex(random_bytes(16));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+3 days'));
            $stmt = $db->prepare("INSERT INTO tokens (email, api_key) VALUES (:email, :apiKey) ON DUPLICATE KEY UPDATE token = :token, expires_at = :expiresAt");
            $stmt->execute([':email' => $email, ':apiKey' => $apiKey, ':expiresAt' => $expiresAt, ':token' => $token]);
            return ["token" => $token];
        }
    }

    public static function validateAccessToken($token) {
        if ($token == null) {
            return false;
        }
        
        $db = database::getConnection();
        $stmt = $db->prepare("SELECT expires_at FROM tokens WHERE token = :token LIMIT 1");
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$result || (strtotime($result['expires_at']) < time())) {
            return false;
        } else {
            return true;
        }
    }
}
?>
