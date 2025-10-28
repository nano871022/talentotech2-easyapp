<?php
// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Set content type to JSON
header("Content-Type: application/json");

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\RequestController;
use App\Middleware\JwtMiddleware;

// Routing
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'] ?? '/';

$routes = [
    'POST' => [
        '/api/v1/requests' => [RequestController::class, 'createRequest'],
        '/api/v1/requests/correct-data' => [RequestController::class, 'correctData'],
    ],
    'GET' => [
        '/api/v1/requests' => [RequestController::class, 'getRequests'],
        '~^/api/v1/requests/\d+$~' => [RequestController::class, 'getRequest'],
        '~^/api/v1/requests/summary/\d+$~' => [RequestController::class, 'getRequestSummary'],
    ],
    'PUT' => [
        '~^/api/v1/requests/\d+/status$~' => [RequestController::class, 'updateStatus'],
    ],
];

// Find the handler for the current request
$handler = null;
if (isset($routes[$method])) {
    if (isset($routes[$method][$path])) {
        $handler = $routes[$method][$path];
    } else {
        foreach ($routes[$method] as $route => $action) {
            if (substr($route, 0, 1) === '~') {
                if (preg_match($route, $path)) {
                    $handler = $action;
                    break;
                }
            }
        }
    }
}

// Dispatching
if ($handler) {
    $controllerName = $handler[0];
    $methodName = $handler[1];

    $jwtMiddleware = new JwtMiddleware();
    if (!$jwtMiddleware->handle()) {
        exit;
    }

    try {
        $controller = new $controllerName();
        $controller->$methodName();
    } catch (Exception $e) {
        http_response_code(500);
        error_log('API Dispatch Error: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal Server Error', 'message' => 'The server encountered an unexpected condition.']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'message' => 'The requested endpoint does not exist.']);
}
