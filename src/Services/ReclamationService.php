<?php

namespace App\Services;

use App\Repositories\Impl\ReclamationRepository;

class ReclamationService
{
    private ReclamationRepository $reclamationRepository;

    public function __construct(ReclamationRepository $reclamationRepository)
    {
        $this->reclamationRepository = $reclamationRepository;
    }

    public function createReclamation(int $userId, int $logementId, string $message): bool
    {
        return $this->reclamationRepository->create($userId, $logementId, $message);
    }

    public function getReclamationsForHost(int $hostId): array
    {
        return $this->reclamationRepository->findByHost($hostId);
    }

    public function getAllReclamations(): array
    {
        return $this->reclamationRepository->findAll();
    }
}
