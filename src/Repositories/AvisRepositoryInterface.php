<?php

namespace App\Repositories;

use App\Entities\Models\Avis;

interface AvisRepositoryInterface
{
    public function save(Avis $avis): int;
    public function findByLogement(int $logementId): array;
    public function getAverageRating(int $logementId): float;
}
