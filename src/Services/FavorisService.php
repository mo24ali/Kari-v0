<?php

namespace App\Services;

use App\Repositories\FavorisRepository;

class FavorisService
{
    private FavorisRepository $favorisRepository;

    public function __construct(FavorisRepository $favorisRepository)
    {
        $this->favorisRepository = $favorisRepository;
    }

    public function addToFavoris(int $userId, int $logementId): bool
    {
        if ($this->favorisRepository->isFavoris($userId, $logementId)) {
            return false; 
        }
        return $this->favorisRepository->add($userId, $logementId);
    }

    public function removeFromFavoris(int $userId, int $logementId): bool
    {
        return $this->favorisRepository->remove($userId, $logementId);
    }

    public function getUserFavoris(int $userId): array
    {
        return $this->favorisRepository->findByUser($userId);
    }

    public function isFavoris(int $userId, int $logementId): bool
    {
        return $this->favorisRepository->isFavoris($userId, $logementId);
    }
}
