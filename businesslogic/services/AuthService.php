<?php

require_once __DIR__ . '/../repositories/AdminRepository.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthService
{
    private AdminRepository $adminRepository;

    public function __construct()
    {
        $this->adminRepository = new AdminRepository();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Autentica a un administrador y gestiona la sesión.
     *
     * @param string $usuario
     * @param string $password
     * @return bool `true` si el login es exitoso, `false` en caso contrario.
     */
    public function login(string $usuario, string $password): bool
    {
        $admin = $this->adminRepository->findByUsername($usuario);

        if ($admin && password_verify($password, $admin->getPasswordHash())) {
            $_SESSION['admin_id'] = $admin->getId();
            $_SESSION['admin_name'] = $admin->getNombre() ?: $admin->getUsuario();
            return true;
        }

        return false;
    }

    /**
     * Cierra la sesión del administrador.
     */
    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Verifica si hay un administrador autenticado.
     *
     * @return bool `true` si está autenticado, `false` en caso contrario.
     */
    public function checkAuth(): bool
    {
        return isset($_SESSION['admin_id']);
    }
}