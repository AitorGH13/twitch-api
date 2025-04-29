<?php

declare(strict_types=1);

namespace App\TwitchApi;

use App\TwitchApi\Database;
use PDO;
use DateTime;

class AuthController
{
    public function registerEmail(string $email): array
    {
        if ($email === '') {
            http_response_code(400);
            echo json_encode(['error' => 'The email is mandatory.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'The email must be a valid email address.']);
            exit;
        }

        $dbService = new App\TwitchApi\Database();
        $dbConnection = $dbService->getConnection();
        $apiKey      = bin2hex(random_bytes(8));
        $sqlInsert   = <<<'SQL'
INSERT INTO tokens (email, api_key)
VALUES (:email, :apiKey)
ON DUPLICATE KEY UPDATE
    api_key = :apiKey
SQL;

        $statement = $dbConnection->prepare($sqlInsert);
        $statement->execute([
            ':email'  => $email,
            ':apiKey' => $apiKey,
        ]);

        return ['api_key' => $apiKey];
    }

    public function createAccessToken(string $email, string $apiKey): array
    {
        if ($email === '') {
            http_response_code(400);
            echo json_encode(['error' => 'The email is mandatory.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'The email must be a valid email address.']);
            exit;
        }

        if ($apiKey === '') {
            http_response_code(400);
            echo json_encode(['error' => 'The api_key is mandatory.']);
            exit;
        }

        $dbService = new App\TwitchApi\Database();
        $dbConnection = $dbService->getConnection();
        $stmtCheck    = $dbConnection->prepare(
            'SELECT expires_at FROM tokens WHERE email = :email AND api_key = :apiKey LIMIT 1'
        );
        $stmtCheck->execute([
            ':email'  => $email,
            ':apiKey' => $apiKey,
        ]);

        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            http_response_code(401);
            return ['error' => 'Unauthorized. API access token is invalid.'];
        }

        $token     = bin2hex(random_bytes(16));
        $expiresAt = (new DateTime('+3 days'))->format('Y-m-d H:i:s');
        $sqlUpdate = <<<'SQL'
INSERT INTO tokens (email, api_key, token, expires_at)
VALUES (:email, :apiKey, :token, :expiresAt)
ON DUPLICATE KEY UPDATE
    token      = :token,
    expires_at = :expiresAt
SQL;

        $stmtUpdate = $dbConnection->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':email'     => $email,
            ':apiKey'    => $apiKey,
            ':token'     => $token,
            ':expiresAt' => $expiresAt,
        ]);

        return ['token' => $token];
    }

    public function validateAccessToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        $dbService = new App\TwitchApi\Database();
        $dbConnection = $dbService->getConnection();
        $stmtQuery    = $dbConnection->prepare(
            'SELECT expires_at FROM tokens WHERE token = :token LIMIT 1'
        );
        $stmtQuery->execute([':token' => $token]);

        $result = $stmtQuery->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return false;
        }

        return \strtotime($result['expires_at']) >= \time();
    }
}
