<?php

namespace App\Services;

use App\Repositories\LogementRepository;
use App\Entities\Models\Logement;
use Exception;

class LogementService
{
    private LogementRepository $logementRepository;

    public function __construct(LogementRepository $logementRepository)
    {
        $this->logementRepository = $logementRepository;
    }

    public function getAllLogements(): array
    {
        return $this->logementRepository->findAll();
    }

    public function getLogementById(int $id): ?Logement
    {
        return $this->logementRepository->findById($id);
    }

    public function getLogementByIdAsArray(int $id): ?array
    {
        return $this->logementRepository->findByIdAsArray($id);
    }

    public function getLogementsByOwner(int $ownerId): array
    {
        return $this->logementRepository->findByOwner($ownerId);
    }

    public function createLogement(array $data, int $ownerId): Logement
    {
        $errors = $this->validateLogementData($data);
        
        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }

        $logement = new Logement(
            $ownerId,
            (float) $data['price'],
            trim($data['address'] ?? '')
        );

        $this->logementRepository->save($logement);
        return $logement;
    }

    public function updateLogement(int $id, array $data, int $ownerId): bool
    {
        $logement = $this->logementRepository->findById($id);
        
        if (!$logement) {
            throw new Exception("Logement non trouvé.");
        }

        if ($logement->getIdOwner() != $ownerId) {
            throw new Exception("Vous n'avez pas la permission de modifier ce logement.");
        }

        if (isset($data['price'])) {
            $logement->setPrice((float) $data['price']);
        }
        if (isset($data['address'])) {
            $logement->setAddress(trim($data['address']));
        }

        return $this->logementRepository->update($logement);
    }

    public function deleteLogement(int $id, int $ownerId): bool
    {
        $logement = $this->logementRepository->findById($id);
        
        if (!$logement) {
            throw new Exception("Logement non trouvé.");
        }

        if ($logement->getIdOwner() != $ownerId) {
            throw new Exception("Vous n'avez pas la permission de supprimer ce logement.");
        }

        return $this->logementRepository->delete($id);
    }

    private function validateLogementData(array $data): array
    {
        $errors = [];

        if (empty($data['address'])) {
            $errors[] = "L'adresse est requise.";
        }

        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $errors[] = "Le prix doit être un nombre positif.";
        }

        return $errors;
    }
}
