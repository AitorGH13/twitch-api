<?php
header("Content-Type: application/json");

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/StreamerController.php';
require_once __DIR__ . '/src/StreamController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit;
}

if (preg_match('/^\/analytics\/user$/', $uri)) {
    $headers = getallheaders();
    $authToken = $headers['Authorization'] ?? null;
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid or missing 'id' parameter."]);
        exit;
    }
    if (!$authToken) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
        exit;
    }
    Database::validateAccessToken(str_replace("Bearer ", "", $authToken));
    echo json_encode(StreamerController::getStreamerById($id));
    exit;
}

if (preg_match('/^\/analytics\/streams$/', $uri)) {
    echo json_encode(StreamController::getLiveStreams());
    exit;
}

if (preg_match('/^\/analytics\/streams\/enriched$/', $uri)) {
    $limit = $_GET['limit'] ?? 3;
    echo json_encode(StreamController::getTopEnrichedStreams($limit));
    exit;
}

http_response_code(500);
echo json_encode(["error" => "Internal server error."]);
