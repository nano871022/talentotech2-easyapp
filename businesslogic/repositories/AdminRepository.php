<?php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/Database.php';

/**
 * AdminRepository (Data Access Object)
 *
 * Proporciona los métodos para interactuar con la tabla `admins`.
 */
class AdminRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca un administrador por su nombre de usuario.
     *
     * @param string $usuario El nombre de usuario del administrador.
     * @return Admin|null Devuelve un objeto Admin si lo encuentra, o null si no.
     */
    public function findByUsername(string $usuario): ?Admin
    {
        $sql = "SELECT id, usuario, password_hash, nombre FROM admins WHERE usuario = :usuario";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch();

            if ($row) {
                // Crear y devolver un objeto Admin con los datos de la BD
                return new Admin(
                    $row['usuario'],
                    $row['password_hash'],
                    $row['nombre'],
                    $row['id']
                );
            }
        } catch (PDOException $e) {
            error_log('AdminRepository Error - findByUsername: ' . $e->getMessage());
            return null;
        }

        return null;
    }
}