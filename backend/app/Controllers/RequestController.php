<?php

namespace App\Controllers;

use App\Services\RequestService;

class RequestController
{
    private ?RequestService $requestService = null;

    /**
     * Lazily instantiates the RequestService.
     */
    private function getService(): RequestService
    {
        if ($this->requestService === null) {
            $this->requestService = new RequestService();
        }
        return $this->requestService;
    }

    /**
     * Handles the creation of a new advisory request.
     * Expects a JSON body with "nombre", "correo", and optional "telefono".
     */
    public function createRequest(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['nombre']) || !isset($data['correo'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid JSON or missing required fields.']);
            return;
        }

        $nombre = trim($data['nombre']);
        $correo = trim($data['correo']);
        $telefono = isset($data['telefono']) ? trim($data['telefono']) : null;

        try {
            $newRequest = $this->getService()->createAdvisoryRequest($nombre, $correo, $telefono);
            if ($newRequest) {
                http_response_code(201); // Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Request submitted successfully.',
                    'request' => [
                        'id' => $newRequest->getId(),
                        'nombre' => $newRequest->getNombre(),
                        'correo' => $newRequest->getCorreo(),
                        'telefono' => $newRequest->getTelefono()
                    ]
                ]);
            } else {
                // This could happen if, for example, the email is a duplicate
                http_response_code(409); // Conflict
                echo json_encode(['error' => 'Conflict', 'message' => 'The request could not be processed. The email may already exist.']);
            }
        } catch (\Exception $e) {
            error_log('Request Creation Error: ' . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Internal Server Error', 'message' => 'An unexpected error occurred while processing your request.']);
        }
    }

    /**
     * Handles fetching all advisory requests for the dashboard.
     */
    public function getRequests(): void
    {
        try {
            $requests = $this->getService()->fetchAllRequests();

            // Format the data for the frontend
            $formattedRequests = array_map(function ($request) {
                return [
                    'id' => $request->getId(),
                    'nombre' => $request->getNombre(),
                    'correo' => $request->getCorreo(),
                    'telefono' => $request->getTelefono(),
                    'estado' => $request->getEstado(),
                    'fecha_solicitud' => (new \DateTime($request->getCreatedAt()))->format('Y-m-d H:i:s'),
                ];
            }, $requests);

            http_response_code(200);
            echo json_encode($formattedRequests);

        } catch (\Exception $e) {
            error_log('Fetch Requests Error: ' . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Internal Server Error', 'message' => 'An error occurred while fetching requests.']);
        }
    }

    /**
     * Handles fetching a single request summary.
     */
    public function getRequestSummary(): void
    {
        // This is a simplified way to get the ID from the URL,
        // assuming a URL structure like /v1/requests/summary/123
        $pathParts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        $id = end($pathParts);

        if (!is_numeric($id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid request ID.']);
            return;
        }

        try {
            $summary = $this->getService()->fetchRequestSummary((int)$id);

            if ($summary) {
                http_response_code(200);
                echo json_encode($summary);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Not Found', 'message' => 'Request summary not found.']);
            }
        } catch (\Exception $e) {
            error_log('Fetch Request Summary Error: ' . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Internal Server Error', 'message' => 'An error occurred while fetching the request summary.']);
        }
    }
}