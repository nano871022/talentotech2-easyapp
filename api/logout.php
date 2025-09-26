<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Ajustar en producción
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../businesslogic/services/AuthService.php';

// Gestionar petición OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Solo permitir peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit();
}

$authService = new AuthService();
$authService->logout();

http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Logout exitoso.']);