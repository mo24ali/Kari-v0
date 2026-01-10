<?php

namespace App\Services;

use App\Repositories\AvisRepositoryInterface;
use App\Entities\Models\Avis;
use Exception;

class AvisService
{
    private AvisRepositoryInterface $avisRepository;

    public function __construct(AvisRepositoryInterface $avisRepository)
    {
        $this->avisRepository = $avisRepository;
    }

    public function createReview(int $reservationId, int $rating, string $comment): Avis
    {
        if ($rating < 1 || $rating > 5) {
            throw new Exception("La note doit être comprise entre 1 et 5.");
        }

        $avis = new Avis($reservationId, $rating, trim($comment));

        $this->avisRepository->save($avis);
        return $avis;
    }

    public function getLogementReviews(int $logementId): array
    {
        return $this->avisRepository->findByLogement($logementId);
    }

    public function getAverageRating(int $logementId): float
    {
        return $this->avisRepository->getAverageRating($logementId);
    }
}
