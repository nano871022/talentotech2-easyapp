<?php

require_once __DIR__ . '/../model/Contacto.php';
require_once __DIR__ . '/Database.php';

/**
 * ContactoDAO (Data Access Object)
 *
 * Proporciona los métodos para interactuar con la tabla `contactos`.
 */
class ContactoDAO
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Inserta una nueva solicitud de contacto en la base de datos.
     *
     * @param Contacto $contacto El objeto Contacto con los datos a insertar.
     * @return bool Devuelve true si la inserción fue exitosa, false en caso contrario.
     */
    public function create(Contacto $contacto): bool
    {
        // Generar tokens únicos para la gestión de la suscripción
        $baja_token = hash('sha256', bin2hex(random_bytes(32)) . $contacto->getCorreo());
        $update_token = hash('sha256', bin2hex(random_bytes(32)) . $contacto->getCorreo());

        $sql = "INSERT INTO contactos (nombre, correo, telefono, consentimiento, baja_token, update_token)
                VALUES (:nombre, :correo, :telefono, 1, :baja_token, :update_token)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':nombre', $contacto->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':correo', $contacto->getCorreo(), PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $contacto->getTelefono(), PDO::PARAM_STR);
            $stmt->bindValue(':baja_token', $baja_token, PDO::PARAM_STR);
            $stmt->bindValue(':update_token', $update_token, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Manejar error, por ejemplo, si el correo ya existe (llave única)
            error_log('ContactoDAO Error - create: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupera todas las solicitudes de contacto de la base de datos.
     *
     * @return array Un array de objetos Contacto.
     */
    public function findAll(): array
    {
        $sql = "SELECT id, nombre, correo, telefono, estado, created_at
                FROM contactos
                ORDER BY created_at DESC";

        $contactos = [];
        try {
            $stmt = $this->db->query($sql);
            while ($row = $stmt->fetch()) {
                $contactos[] = new Contacto(
                    $row['nombre'],
                    $row['correo'],
                    $row['telefono'],
                    $row['id'],
                    $row['estado'],
                    $row['created_at']
                );
            }
        } catch (PDOException $e) {
            error_log('ContactoDAO Error - findAll: ' . $e->getMessage());
        }
        return $contactos;
    }
}