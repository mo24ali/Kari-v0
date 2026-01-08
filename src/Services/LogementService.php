<?php

namespace App\Services;

use App\Repositories\Impl\LogementRepository;
use App\Entities\Models\Logement;
use App\Repositories\Impl\ImageRepository;
use Exception;

class LogementService
{
    private LogementRepository $logementRepository;
    private $imageRepository;

    public function __construct(LogementRepository $logementRepository)
    {
        $this->logementRepository = $logementRepository;
        $this->imageRepository = new ImageRepository();
    }

    public function getAllLogements(): array
    {
        $logements = $this->logementRepository->findAll();
        return $this->attachImages($logements);
    }

    public function getLogementById(int $id): ?Logement
    {
        return $this->logementRepository->findById($id);
    }

    public function getLogementByIdAsArray(int $id): ?array
    {
        $logement = $this->logementRepository->findByIdAsArray($id);
        if ($logement) {
            // Single fetching is fine here, or reuse attachImages
            $images = $this->imageRepository->findByLogement($id);
            $logement['images'] = $images;
            if (empty($logement['primary_image']) && !empty($images)) {
                $logement['primary_image'] = $images[0]['image_path'];
            }
        }
        return $logement;
    }

    public function getLogementsByOwner(int $ownerId): array
    {
        $logements = $this->logementRepository->findByOwner($ownerId);
        return $this->attachImages($logements);
    }

    public function searchLogements(array $filters): array
    {
        $logements = $this->logementRepository->search($filters);
        return $this->attachImages($logements);
    }
    public function searchLogementsByPrice(int $maxPrice, int $minPrice): array
    {
        $logements = $this->logementRepository->findByPriceRange($minPrice, $maxPrice);
        return $this->attachImages($logements);
    }

    public function getReservedDates(int $logementId): array
    {
        return $this->logementRepository->getReservedDates($logementId);
    }

    private function attachImages(array $logements): array
    {
        if (empty($logements)) {
            return [];
        }

        $ids = array_column($logements, 'id');
        $imagesGrouped = $this->imageRepository->findByLogementIds($ids);

        foreach ($logements as &$logement) {
            $logementImages = $imagesGrouped[$logement['id']] ?? [];
            $logement['images'] = $logementImages;

            // Ensure primary image is set if missing BUT images exist
            // (Repository findAll joins primary_image, but explicit check helps)
            if (empty($logement['primary_image']) && !empty($logementImages)) {
                $logement['primary_image'] = $logementImages[0]['image_path'];
            }
        }
        return $logements;
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
