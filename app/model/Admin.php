<?php

/**
 * Entidad Admin
 * Representa un registro de la tabla `admins`.
 */
class Admin
{
    public function __construct(
        private string $usuario,
        private string $password_hash,
        private ?string $nombre = null,
        private ?int $id = null
    ) {}

    // --- Getters ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): string
    {
        return $this->usuario;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }
}