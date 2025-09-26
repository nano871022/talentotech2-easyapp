<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Ajustar en producción
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../businesslogic/services/AuthService.php';
require_once __DIR__ . '/../businesslogic/services/ContactService.php';

// Gestionar petición OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$authService = new AuthService();
if (!$authService->checkAuth()) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado.']);
    exit();
}

// Solo permitir peticiones GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit();
}

$contactService = new ContactService();
$solicitudes = $contactService->getAllSolicitudes();

// Convertir objetos a un array asociativo para la respuesta JSON
$response_data = array_map(function($solicitud) {
    return [
        'id' => $solicitud->getId(),
        'nombre' => $solicitud->getNombre(),
        'correo' => $solicitud->getCorreo(),
        'telefono' => $solicitud->getTelefono(),
        'estado' => $solicitud->getEstado(),
        'created_at' => $solicitud->getCreatedAt(),
    ];
}, $solicitudes);

http_response_code(200);
echo json_encode(['success' => true, 'data' => $response_data]);