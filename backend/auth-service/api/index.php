<?php
// CORS headers are now handled by nginx - no need to set them here
// Handle preflight requests are also handled by nginx

// Set content type to JSON
header("Content-Type: application/json");

// Include bootstrap file
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\AuthController;

// Routing
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'] ?? '/';

$routes = [
    'POST' => [
        '/api/v1/auth/login' => [AuthController::class, 'login'],
        '/api/v1/auth/register' => [AuthController::class, 'register'],
    ],
];

$handler = null;
if (isset($routes[$method][$path])) {
    $handler = $routes[$method][$path];
}

if ($handler) {
    $controllerName = $handler[0];
    $methodName = $handler[1];

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
