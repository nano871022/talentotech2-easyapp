<?php
// Add debugging logs
error_log('=== ADVISE SERVICE REQUEST DEBUG ===');
error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
error_log('REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'NOT_SET'));
error_log('HTTP_HOST: ' . ($_SERVER['HTTP_HOST'] ?? 'NOT_SET'));
error_log('REMOTE_ADDR: ' . ($_SERVER['REMOTE_ADDR'] ?? 'NOT_SET'));
error_log('User-Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'NOT_SET'));

// CORS headers are now handled by nginx - no need to set them here
// Handle preflight requests are also handled by nginx

// Set content type to JSON
header("Content-Type: application/json");

// Include bootstrap file
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\RequestController;
use App\Middleware\JwtMiddleware;

// Routing
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query parameters from path for routing
$pathParts = parse_url($path);
$path = $pathParts['path'] ?? '/';

// Debug logging
error_log('Parsed path: ' . $path);
error_log('Method: ' . $method);

// Clean the path - remove trailing slash for exact matching
$cleanPath = rtrim($path, '/');
if (empty($cleanPath)) {
    $cleanPath = '/';
}
error_log('Clean path: ' . $cleanPath);

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
error_log('Looking for route in method: ' . $method);

if (isset($routes[$method])) {
    // First try exact match with clean path
    if (isset($routes[$method][$cleanPath])) {
        $handler = $routes[$method][$cleanPath];
        error_log('Found exact match for: ' . $cleanPath);
    } else {
        // Then try regex patterns with both original and clean path
        foreach ($routes[$method] as $route => $action) {
            if (substr($route, 0, 1) === '~') {
                if (preg_match($route, $path) || preg_match($route, $cleanPath)) {
                    $handler = $action;
                    error_log('Found regex match for route: ' . $route . ' with path: ' . $path);
                    break;
                }
            }
        }
    }
} else {
    error_log('Method not found in routes: ' . $method);
}

// Dispatching
if ($handler) {
    $controllerName = $handler[0];
    $methodName = $handler[1];
    
    error_log('Handler found - Controller: ' . $controllerName . ', Method: ' . $methodName);

    // Define routes that don't require authentication
    $publicRoutes = [
        'POST' => ['/api/v1/requests']
    ];
    
    // Check if current route requires authentication
    $requiresAuth = true;
    if (isset($publicRoutes[$method])) {
        foreach ($publicRoutes[$method] as $publicRoute) {
            if ($cleanPath === $publicRoute) {
                $requiresAuth = false;
                error_log('Public route detected, skipping JWT validation: ' . $cleanPath);
                break;
            }
        }
    }

    // Apply JWT middleware only for protected routes
    if ($requiresAuth) {
        $jwtMiddleware = new JwtMiddleware();
        if (!$jwtMiddleware->handle()) {
            error_log('JWT middleware failed');
            exit;
        }
    }

    try {
        $controller = new $controllerName();
        $controller->$methodName();
        error_log('Controller method executed successfully');
    } catch (Exception $e) {
        http_response_code(500);
        error_log('API Dispatch Error: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal Server Error', 'message' => 'The server encountered an unexpected condition.']);
    }
} else {
    error_log('No handler found for path: ' . $path . ' (clean: ' . $cleanPath . ') with method: ' . $method);
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'message' => 'The requested endpoint does not exist.']);
}
