<?php

namespace App\Services;

use App\Repositories\Impl\UserRepository;
use App\Repositories\LogementRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\ImageRepository;

class AdminService
{
    private UserRepository $userRepository;
    private LogementRepository $logementRepository;
    private ReservationRepository $reservationRepository;
    private ImageRepository $imageRepository;

    public function __construct(
        UserRepository $userRepository,
        LogementRepository $logementRepository,
        ReservationRepository $reservationRepository,
        ImageRepository $imageRepository
    ) {
        $this->userRepository = $userRepository;
        $this->logementRepository = $logementRepository;
        $this->reservationRepository = $reservationRepository;
        $this->imageRepository = $imageRepository;
    }

    public function getDashboardStats(): array
    {
        return [
            'total_users' => $this->userRepository->count(),
            'total_hosts' => $this->userRepository->countByRole('host'),
            'total_travellers' => $this->userRepository->countByRole('traveller'),
            'total_admins' => $this->userRepository->countByRole('admin'),
            'total_logements' => $this->logementRepository->count(),
            'total_reservations' => $this->getTotalReservations(),
            'total_reviews' => $this->getTotalReviews(),
            'total_favoris' => $this->getTotalFavoris(),
            'total_images' => $this->getTotalImages(),
            'recent_users' => $this->userRepository->findAll(10, 0),
            'recent_logements' => $this->logementRepository->findAll(),
            'users_by_role' => [
                'host' => $this->userRepository->countByRole('host'),
                'traveller' => $this->userRepository->countByRole('traveller'),
                'admin' => $this->userRepository->countByRole('admin')
            ]
        ];
    }

    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        return $this->userRepository->findAll($limit, $offset);
    }

    public function getUsersByRole(string $role, int $limit = 50, int $offset = 0): array
    {
        return $this->userRepository->findByRole($role, $limit, $offset);
    }

    private function getTotalReservations(): int
    {
        $sql = "SELECT COUNT(*) as total FROM reservation";
        $db = \App\core\Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function getTotalReviews(): int
    {
        $sql = "SELECT COUNT(*) as total FROM Review";
        $db = \App\core\Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function getTotalFavoris(): int
    {
        $sql = "SELECT COUNT(*) as total FROM Favoris";
        $db = \App\core\Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function getTotalImages(): int
    {
        $sql = "SELECT COUNT(*) as total FROM images";
        $db = \App\core\Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
}
