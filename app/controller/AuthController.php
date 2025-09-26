<?php

session_start();

// Incluir los DAOs, Modelos y el nuevo View helper
require_once __DIR__ . '/../dao/AdminDAO.php';
require_once __DIR__ . '/../dao/ContactoDAO.php';
require_once __DIR__ . '/../model/Admin.php';
require_once __DIR__ . '/../model/Contacto.php';
require_once __DIR__ . '/../core/View.php';

class AuthController
{
    /**
     * Gestiona el proceso de registro de una nueva solicitud de contacto.
     */
    public function handleRegister()
    {
        $data = ['page_title' => 'Solicitud de Asesoría'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? null);

            if (empty($nombre) || empty($correo)) {
                $data['error_message_html'] = '<div class="alert alert-danger">El nombre y el correo electrónico son obligatorios.</div>';
            } else {
                $contactoDAO = new ContactoDAO();
                $contacto = new Contacto($nombre, $correo, $telefono);

                if ($contactoDAO->create($contacto)) {
                    header("Location: register_success.php");
                    exit();
                } else {
                    $data['error_message_html'] = '<div class="alert alert-danger">Error al enviar la solicitud. Es posible que el correo ya esté registrado.</div>';
                }
            }
        }

        View::render('auth/register_form', $data);
    }

    /**
     * Gestiona el proceso de inicio de sesión de un administrador.
     */
    public function handleLogin()
    {
        $data = ['page_title' => 'Acceso de Administrador'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';

            $adminDAO = new AdminDAO();
            $admin = $adminDAO->findByUsername($usuario);

            if ($admin && password_verify($password, $admin->getPasswordHash())) {
                $_SESSION['admin_id'] = $admin->getId();
                $_SESSION['admin_name'] = $admin->getNombre() ?: $admin->getUsuario();
                header("Location: dashboard.php");
                exit();
            } else {
                $data['error_message_html'] = '<div class="alert alert-danger">Usuario o contraseña incorrectos.</div>';
            }
        }

        View::render('auth/login_form', $data);
    }
}