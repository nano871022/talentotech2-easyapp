<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Request;
use PDO;
use PDOException;

/**
 * RequestRepository
 * Provides methods to interact with the `contactos` table (advisory requests).
 */
class RequestRepository
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Saves a new advisory request to the database.
     *
     * @param Request $request The Request object with data to insert.
     * @return Request|null The created Request object with its new ID, or null on failure.
     */
    public function save(Request $request): ?Request
    {
        if ($this->db === null) {
            error_log('RequestRepository Error: Database connection is not available.');
            return null;
        }

        // Simplified SQL statement focusing on core request data
        $sql = "INSERT INTO contactos (nombre, correo, telefono) VALUES (:nombre, :correo, :telefono)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':nombre', $request->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':correo', $request->getCorreo(), PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $request->getTelefono(), PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Return the newly created request object, now with an ID
                $id = $this->db->lastInsertId();
                return $this->findById($id);
            }
        } catch (PDOException $e) {
            // Log error, e.g., for duplicate email (unique key constraint)
            error_log('RequestRepository Error - save: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Retrieves all advisory requests from the database.
     *
     * @return array An array of Request objects.
     */
    public function findAll(): array
    {
        if ($this->db === null) {
            error_log('RequestRepository Error: Database connection is not available.');
            return [];
        }

        $sql = "SELECT id, nombre, correo, telefono, estado, created_at FROM contactos ORDER BY created_at DESC";
        $requests = [];

        try {
            $stmt = $this->db->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $requests[] = new Request(
                    $row['nombre'],
                    $row['correo'],
                    $row['telefono'],
                    $row['id'],
                    $row['estado'],
                    $row['created_at']
                );
            }
        } catch (PDOException $e) {
            error_log('RequestRepository Error - findAll: ' . $e->getMessage());
        }

        return $requests;
    }

    /**
     * Finds a single request by its ID.
     *
     * @param int $id The ID of the request.
     * @return Request|null
     */
    public function findById(int $id): ?Request
    {
        if ($this->db === null) {
            return null;
        }

        $sql = "SELECT * FROM contactos WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                 return new Request(
                    $row['nombre'],
                    $row['correo'],
                    $row['telefono'],
                    $row['id'],
                    $row['estado'],
                    $row['created_at']
                );
            }
        } catch (PDOException $e) {
            error_log('RequestRepository Error - findById: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Finds the necessary data for a request summary by its ID.
     *
     * @param int $id The ID of the request.
     * @return array|null An associative array with summary data or null if not found.
     */
    public function findSummaryById(int $id): ?array
    {
        $request = $this->findById($id);
        if (!$request) {
            return null;
        }

        // Fetch associated languages.
        // Note: The table `solicitud_idiomas` and column `idioma_nombre` are assumed
        // based on the requirements, as the schema details are not provided.
        $languages = [];
        if ($this->db) {
            $sql = "SELECT idioma_nombre FROM solicitud_idiomas WHERE solicitud_id = :id";
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $languages[] = $row['idioma_nombre'];
                }
            } catch (PDOException $e) {
                error_log('RequestRepository Error - findSummaryById languages: ' . $e->getMessage());
                // Depending on requirements, you might want to return null or an empty array for languages.
            }
        }

        return [
            'nombreSolicitante' => $request->getNombre(),
            'estado' => $request->getEstado(),
            'idiomasSolicitados' => $languages,
            'requestId' => $request->getId()
        ];
    }
}