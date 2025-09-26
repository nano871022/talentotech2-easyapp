<?php
// Punto de entrada para el registro de usuarios.
require_once __DIR__ . '/app/controller/AuthController.php';

$authController = new AuthController();
$authController->handleRegister();