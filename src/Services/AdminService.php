<?php

namespace App\Services;

use App\Repositories\Impl\UserRepository;
use App\Repositories\Impl\LogementRepository;
use App\Repositories\Impl\ReservationRepository;
use App\Repositories\Impl\ImageRepository;
use App\core\Database;
use PDO;
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
            'total_revenue' => $this->getTotalRevenue(),
            'total_reserved_logements' => $this->getReservedLogementsCount(),
            'recent_users' => $this->userRepository->findAll(),
            'recent_logements' => $this->logementRepository->findAll(),
            'recent_reservations' => $this->getRecentReservations(5),
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
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function getTotalReviews(): int
    {
        $sql = "SELECT COUNT(*) as total FROM Review";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function getTotalFavoris(): int
    {
        $sql = "SELECT COUNT(*) as total FROM Favoris";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function getTotalImages(): int
    {
        $sql = "SELECT COUNT(*) as total FROM images";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function getTotalRevenue(): float
    {
        $sql = "SELECT SUM(l.price * (DATEDIFF(r.end_date, r.start_date) + 1)) as total 
                FROM reservation r 
                JOIN logement l ON r.id_log = l.id";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($result['total'] ?? 0);
    }

    private function getReservedLogementsCount(): int
    {
        $sql = "SELECT COUNT(DISTINCT id_log) as total FROM reservation";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function getRecentReservations(int $limit): array
    {
        $sql = "SELECT r.*, l.address, u.firstname, u.lastname, l.price 
                FROM reservation r 
                JOIN logement l ON r.id_log = l.id 
                JOIN users u ON r.id_user = u.id 
                ORDER BY r.id DESC LIMIT ?";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
