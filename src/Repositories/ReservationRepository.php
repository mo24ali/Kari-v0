<?php

namespace App\Repositories;

use App\core\Database;
use PDO;

class ReservationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(array $data): int
    {
        $sql = "INSERT INTO reservation (id_user, id_log, start_date, end_date) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['id_user'],
            $data['id_log'],
            $data['start_date'],
            $data['end_date']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByUser(int $userId): array
    {
        $sql = "SELECT r.*, l.address, l.price 
                FROM reservation r 
                LEFT JOIN logement l ON r.id_log = l.id 
                WHERE r.id_user = ? 
                ORDER BY r.start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByLogement(int $logementId): array
    {
        $sql = "SELECT r.*, u.firstname, u.lastname, u.email 
                FROM reservation r 
                LEFT JOIN users u ON r.id_user = u.id 
                WHERE r.id_log = ? 
                ORDER BY r.start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAvailability(int $logementId, string $startDate, string $endDate): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM reservation 
                WHERE id_log = ? 
                AND (
                    (start_date <= ? AND end_date >= ?) OR
                    (start_date <= ? AND end_date >= ?) OR
                    (start_date >= ? AND end_date <= ?)
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $logementId,
            $startDate, $startDate,
            $endDate, $endDate,
            $startDate, $endDate
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'] === 0;
    }
}
