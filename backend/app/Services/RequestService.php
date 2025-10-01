<?php

namespace App\Services;

use App\Models\Request;
use App\Repositories\RequestRepository;

class RequestService
{
    private RequestRepository $requestRepository;

    public function __construct()
    {
        $this->requestRepository = new RequestRepository();
    }

    /**
     * Creates a new advisory request after performing validation.
     *
     * @param string $nombre The full name of the applicant.
     * @param string $correo The email of the applicant.
     * @param string|null $telefono The optional phone number of the applicant.
     * @return Request|null The created Request object or null on failure.
     */
    public function createAdvisoryRequest(string $nombre, string $correo, ?string $telefono): ?Request
    {
        // Basic validation
        if (empty($nombre) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            // In a real app, you'd throw a custom validation exception
            return null;
        }

        $request = new Request($nombre, $correo, $telefono);

        return $this->requestRepository->save($request);
    }

    /**
     * Fetches all advisory requests.
     *
     * @return array An array of Request objects.
     */
    public function fetchAllRequests(): array
    {
        return $this->requestRepository->findAll();
    }

    /**
     * Fetches a single advisory request by its ID.
     *
     * @param int $id The ID of the request.
     * @return Request|null The Request object or null if not found.
     */
    public function fetchRequestById(int $id): ?Request
    {
        return $this->requestRepository->findById($id);
    }

    /**
     * Updates the contact status of a request.
     *
     * @param int $id The ID of the request.
     * @param bool $contactado The new contact status.
     * @return bool True on success, false on failure.
     */
    public function updateRequestStatus(int $id, bool $contactado): bool
    {
        return $this->requestRepository->updateStatus($id, $contactado);
    }
}