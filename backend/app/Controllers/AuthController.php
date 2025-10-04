<?php

namespace App\Controllers;

// This will use AuthService, which we will create soon.
use App\Services\AuthService;

class AuthController
{
    private ?AuthService $authService = null;

    /**
     * Handles the login request for an administrator.
     * Expects a JSON body with "usuario" and "password".
     */
    public function login(): void
    {
        // Lazily instantiate the service to avoid issues if a dependency fails
        if ($this->authService === null) {
            $this->authService = new AuthService();
        }

        // Get the raw POST data from the request body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Input validation
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['usuario']) || !isset($data['password'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid JSON or missing credentials.']);
            return;
        }

        $usuario = trim($data['usuario']);
        $password = $data['password'];

        try {
            $admin = $this->authService->authenticate($usuario, $password);

            if ($admin) {
                // In a stateless API, we would issue a token (e.g., JWT).
                // For this simulation, we'll return a success message.
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful.',
                    // Exposing limited admin data is fine for this context
                    'admin' => [
                        'id' => $admin->getId(),
                        'name' => $admin->getNombre(),
                        'username' => $admin->getUsuario()
                    ]
                ]);
            } else {
                http_response_code(401); // Unauthorized
                echo json_encode(['error' => 'Unauthorized', 'message' => 'Invalid username or password.']);
            }
        } catch (\Exception $e) {
            // Log the real error for debugging purposes
            error_log('Login Error: ' . $e->getMessage());

            // Return a generic error message to the client
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Internal Server Error', 'message' => 'An unexpected error occurred.']);
        }
    }

    /**
     * Handles the registration request for a new administrator.
     * Expects a JSON body with "usuario", "password", and "nombre".
     */
    public function register(): void
    {
        if ($this->authService === null) {
            $this->authService = new AuthService();
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['usuario']) || !isset($data['password']) || !isset($data['nombre'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid JSON or missing fields.']);
            return;
        }

        $usuario = trim($data['usuario']);
        $password = $data['password'];
        $nombre = trim($data['nombre']);

        // Basic validation for empty fields
        if (empty($usuario) || empty($password) || empty($nombre)) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Request', 'message' => 'Username, password, and name cannot be empty.']);
            return;
        }

        try {
            $admin = $this->authService->register($usuario, $password, $nombre);

            if ($admin) {
                http_response_code(201); // Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Administrator registered successfully.',
                    'admin' => [
                        'id' => $admin->getId(),
                        'name' => $admin->getNombre(),
                        'username' => $admin->getUsuario()
                    ]
                ]);
            } else {
                // This could be due to a duplicate username or other database error
                http_response_code(409); // Conflict
                echo json_encode(['error' => 'Conflict', 'message' => 'Username may already exist or another error occurred.']);
            }
        } catch (\Exception $e) {
            error_log('Registration Error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => 'An unexpected error occurred during registration.']);
        }
    }
}