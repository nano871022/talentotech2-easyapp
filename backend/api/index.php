<?php
// Set CORS headers to allow requests from the Angular development server
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests sent by browsers
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}
// Set the content type for all API responses to JSON
header("Content-Type: application/json");

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\RequestController;
use App\Controllers\InfoController;
use App\Middleware\JwtMiddleware;

// Basic request routing
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'] ?? '/';

// Define the API routes, including regex for dynamic paths
$routes = [
    'GET' => [
        '/api/v1/info/landing' => [InfoController::class, 'getLandingInfo'],
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

if (!$handler) {
    // Check for dynamic routes (e.g., /v1/requests/summary/{id})
    if ($method === 'GET' && preg_match('/^\/api\/v1\/requests\/summary\/(\d+)$/', $path, $matches)) {
        // We have a match, set the handler manually
        $handler = [RequestController::class, 'getRequestSummary'];
    }
}

// --- Dispatching ---
if ($handler) {
    $controllerName = $handler[0];
    $methodName = $handler[1];

    // Protect all routes handled by RequestController
    if ($controllerName === RequestController::class) {
        $jwtMiddleware = new JwtMiddleware();
        if (!$jwtMiddleware->handle()) {
            // Middleware handles the response, so we just stop execution
            exit;
        }
    }

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
    echo json_encode(['error' => 'Not Found', 'message' => 'The requested endpoint does not exist.',"method"=>"$method","path"=>"$path","next"=>isset($routes[$method][$path]),"Uri"=>"$uri"]);
}