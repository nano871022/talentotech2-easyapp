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

// Find the handler for the current request
$handler = $routes[$method][$path] ?? null;

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