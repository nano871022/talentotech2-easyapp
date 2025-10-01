<?php

namespace App\Models;

/**
 * Entity Request
 * Represents a record from the `contactos` table (an advisory request).
 */
class Request
{
    public function __construct(
        private string $nombre,
        private string $correo,
        private ?string $telefono,
        private ?int $id = null,
        private string $estado = 'nuevo',
        private ?string $created_at = null,
        private ?string $idiomas = null
    ) {}

    // --- Getters ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * Returns the languages as an array.
     * Assumes languages are stored as a comma-separated string.
     * @return array
     */
    public function getIdiomas(): array
    {
        if (empty($this->idiomas)) {
            return [];
        }
        // Trim whitespace from each language and remove empty entries
        return array_filter(array_map('trim', explode(',', $this->idiomas)));
    }
}