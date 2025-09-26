<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Permitir acceso desde cualquier origen (ajustar en producción)
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../businesslogic/services/ContactService.php';

// Solo permitir peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit();
}

// Leer el cuerpo de la petición (JSON)
$input = json_decode(file_get_contents('php://input'), true);

$nombre = $input['nombre'] ?? null;
$correo = $input['correo'] ?? null;
$telefono = $input['telefono'] ?? null;

// Validación básica
if (!$nombre || !$correo) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'El nombre y el correo son obligatorios.']);
    exit();
}

$contactService = new ContactService();
$success = $contactService->createContact($nombre, $correo, $telefono);

if ($success) {
    http_response_code(201); // Created
    echo json_encode(['success' => true, 'message' => 'Solicitud registrada con éxito.']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'No se pudo registrar la solicitud.']);
}