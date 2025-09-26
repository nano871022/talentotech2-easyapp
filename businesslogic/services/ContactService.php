<?php

require_once __DIR__ . '/../repositories/ContactoRepository.php';
require_once __DIR__ . '/../models/Contacto.php';

class ContactService
{
    private ContactoRepository $contactoRepository;

    public function __construct()
    {
        $this->contactoRepository = new ContactoRepository();
    }

    /**
     * Registra una nueva solicitud de contacto.
     *
     * @param string $nombre
     * @param string $correo
     * @param string|null $telefono
     * @return bool `true` si se creó con éxito, `false` en caso contrario.
     */
    public function createContact(string $nombre, string $correo, ?string $telefono): bool
    {
        if (empty($nombre) || empty($correo)) {
            return false; // Validación básica de campos obligatorios.
        }

        $contacto = new Contacto($nombre, $correo, $telefono);

        return $this->contactoRepository->create($contacto);
    }

    /**
     * Obtiene todas las solicitudes de contacto.
     *
     * @return array Un array de objetos Contacto.
     */
    public function getAllSolicitudes(): array
    {
        return $this->contactoRepository->findAll();
    }
}