<?php

/**
 * Entidad Contacto
 * Representa un registro de la tabla `contactos` (una solicitud de asesoría).
 */
class Contacto
{
    public function __construct(
        private string $nombre,
        private string $correo,
        private ?string $telefono,
        private ?int $id = null,
        private string $estado = 'nuevo',
        private ?string $created_at = null
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
}