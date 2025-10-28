<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\AdminRepository;
use Firebase\JWT\JWT;

class AuthService
{
    private AdminRepository $adminRepository;
    private string $jwtSecret;

    public function __construct()
    {
        $this->adminRepository = new AdminRepository();
        // In a real application, this secret should be stored securely (e.g., environment variables)
        $this->jwtSecret = 'your-super-secret-key-for-jwt';
    }

    /**
     * Authenticates an administrator based on username and password.
     *
     * @param string $username The admin's username.
     * @param string $password The admin's plain-text password.
     * @return string|null The JWT on success, null on failure.
     */
    public function authenticate(string $username, string $password): ?string
    {
        $admin = $this->adminRepository->findByUsername($username);

        if (!$admin) {
            return null; // User not found
        }

        // Verify the provided password against the stored hash.
        if (password_verify($password, $admin->getPasswordHash())) {
            // On successful authentication, generate a JWT.
            return $this->generateToken($admin);
        }

        return null;
    }

    /**
     * Generates a JWT for a given administrator.
     *
     * @param Admin $admin The administrator object.
     * @return string The generated JWT.
     */
    private function generateToken(Admin $admin): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 hour
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => [
                'userId' => $admin->getId(),
                // Assuming a default role ID as it is not in the model
                'roleId' => 1,
            ]
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    /**
     * Registers a new administrator.
     *
     * @param string $username
     * @param string $password
     * @param string $name
     * @return Admin|null
     */
    public function register(string $username, string $password, string $name): ?Admin
    {
        // Hash the password for secure storage
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if (!$passwordHash) {
            // Password hashing failed
            return null;
        }

        // Attempt to create the user in the database
        return $this->adminRepository->create($username, $passwordHash, $name);
    }
}