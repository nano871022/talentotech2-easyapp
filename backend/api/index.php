<?php

// Set the content type for all API responses to JSON
header("Content-Type: application/json");

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\RequestController;
use App\Controllers\InfoController;

// Basic request routing
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';

// Define the API routes
$routes = [
    'POST' => [
        '/v1/auth/login' => [AuthController::class, 'login'],
        '/v1/requests' => [RequestController::class, 'createRequest'],
    ],
    'GET' => [
        '/v1/requests' => [RequestController::class, 'getRequests'],
        '/v1/info/landing' => [InfoController::class, 'getLandingInfo'],
    ],
];

// --- Dynamic Route Matching ---
// Find a handler for the current request, including simple dynamic routes.
$handler = $routes[$method][$path] ?? null;

if (!$handler) {
    // Check for dynamic routes (e.g., /v1/requests/summary/{id})
    if ($method === 'GET' && preg_match('/^\/v1\/requests\/summary\/(\d+)$/', $path, $matches)) {
        // We have a match, set the handler manually
        $handler = [RequestController::class, 'getRequestSummary'];
    }
}

// --- Dispatching ---
if ($handler) {
    $controllerName = $handler[0];
    $methodName = $handler[1];

    try {
        $controller = new $controllerName();
        $controller->$methodName();
    } catch (Exception $e) {
        // Generic error for exceptions during controller instantiation or method call
        http_response_code(500);
        error_log('API Dispatch Error: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal Server Error', 'message' => 'The server encountered an unexpected condition.']);
    }
} else {
    // Handle 404 Not Found for all other cases
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'message' => 'The requested endpoint does not exist.']);
}