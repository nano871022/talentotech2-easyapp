<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\AdminRepository;

class AuthService
{
    private AdminRepository $adminRepository;

    public function __construct()
    {
        $this->adminRepository = new AdminRepository();
    }

    /**
     * Authenticates an administrator based on username and password.
     *
     * @param string $username The admin's username.
     * @param string $password The admin's plain-text password.
     * @return Admin|null The Admin object on success, null on failure.
     */
    public function authenticate(string $username, string $password): ?Admin
    {
        $admin = $this->adminRepository->findByUsername($username);

        if (!$admin) {
            return null; // User not found
        }

        // --- Password Decoding Logic ---
        $sharedSecret = 'my-super-secret-key';

        // 1. Decode the Base64 string
        $decoded_password_parts = explode(':', base64_decode($password, true), 2);

        // 2. Check if decoding was successful and the secret matches
        if (count($decoded_password_parts) !== 2 || $decoded_password_parts[0] !== $sharedSecret) {
            // If the secret is wrong or the format is incorrect, fail authentication
            return null;
        }

        // 3. Get the plain-text password
        $plainPassword = $decoded_password_parts[1];
        // --- End of Decoding Logic ---

        // 4. Verify the decoded password against the stored hash
        if (password_verify($plainPassword, $admin->getPasswordHash())) {
            return $admin;
        }

        return null;
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