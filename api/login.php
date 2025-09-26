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

$input = json_decode(file_get_contents('php://input'), true);
$usuario = $input['usuario'] ?? null;
$password = $input['password'] ?? null;

if (!$usuario || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Usuario y contraseña son obligatorios.']);
    exit();
}

$authService = new AuthService();
$success = $authService->login($usuario, $password);

if ($success) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Login exitoso.']);
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas.']);
}