<?php
// Punto de entrada para el inicio de sesión de usuarios.
require_once __DIR__ . '/app/controller/AuthController.php';

$authController = new AuthController();
$authController->handleLogin();