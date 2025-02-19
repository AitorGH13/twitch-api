<?php
header("Content-Type: application/json");

require_once __DIR__ . '/src/StreamerController.php';
require_once __DIR__ . '/src/StreamController.php';
require_once __DIR__ . '/src/AuthController.php';
require_once __DIR__ . '/src/VideoController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (preg_match('/^\/analytics\/user$/', $uri) && ($method == 'GET')) {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid or missing 'id' parameter."]);
        exit;
    }

    echo json_encode(StreamerController::getStreamerById($id));
    exit;
}

if (preg_match('/^\/analytics\/streams$/', $uri) && ($method == 'GET')) {
    echo json_encode(StreamController::getLiveStreams());
    exit;
}

if (preg_match('/^\/analytics\/streams\/enriched$/', $uri) && ($method == 'GET')) {
    $limit = $_GET['limit'] ?? 3;
    echo json_encode(StreamController::getTopEnrichedStreams($limit));
    exit;
}

if (preg_match('/^\/register$/', $uri) && ($method == 'POST')) {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];
    
    if (!isset($email)) {
        http_response_code(400);
        echo json_encode(["error" => "The email is mandatory."]);
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "The email must be a valid email address."]);
        exit;
    }

    echo json_encode(AuthController::registerEmail($email));
    exit;
}

if (preg_match('/^\/token$/', $uri) && ($method == 'POST')) {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];
    $apiKey = $data['api_key'];
    
    if (!isset($email)) {
        http_response_code(400);
        echo json_encode(["error" => "The email is mandatory."]);
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "The email must be a valid email address."]);
        exit;
    } else if (!isset($apiKey)) {
        http_response_code(400);
        echo json_encode(["error" => "The api_key is mandatory."]);
        exit;
    }

    echo json_encode(AuthController::createAccessToken($email, $apiKey));
    exit;
}

if (preg_match('/^\/analytics\/topsofthetops$/', $uri) && ($method == 'GET')) {
    $headers = getallheaders();
    $token = $headers['Authorizationt'] ?? null;
    $since = $_GET['since'] ?? null;
    echo json_encode(VideoController::getTopsOfTheTops($token, $since));
    exit;
}

http_response_code(500);
echo json_encode(["error" => "Internal server error."]);
?>
