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

// Define the API routes, including regex for dynamic paths
$routes = [
    'POST' => [
        '/v1/auth/login' => [AuthController::class, 'login'],
        '/v1/requests' => [RequestController::class, 'createRequest'],
    ],
    'GET' => [
        '/v1/requests' => [RequestController::class, 'getRequests'], // Exact match for list
        '~^/v1/requests/\d+$~' => [RequestController::class, 'getRequest'], // Regex for /v1/requests/{id}
        '/v1/info/landing' => [InfoController::class, 'getLandingInfo'],
    ],
    'PUT' => [
        '~^/v1/requests/\d+/status$~' => [RequestController::class, 'updateStatus'], // Regex for /v1/requests/{id}/status
    ],
];

// Find the handler for the current request
$handler = null;
if (isset($routes[$method])) {
    // First, check for an exact match for static routes
    if (isset($routes[$method][$path])) {
        $handler = $routes[$method][$path];
    } else {
        // If no exact match, iterate through routes to find a regex match for dynamic routes
        foreach ($routes[$method] as $route => $action) {
            // A simple convention: if a route definition starts with a tilde, it's a regex
            if (substr($route, 0, 1) === '~') {
                if (preg_match($route, $path)) {
                    $handler = $action;
                    break; // Stop after finding the first match
                }
            }
        }
    }
}

if ($handler) {
    $controllerName = $handler[0];
    $methodName = $handler[1];

    // Instantiate the controller and call the method
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    // Handle 404 Not Found
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'message' => 'The requested endpoint does not exist.']);
}