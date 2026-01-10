<?php

namespace App\Repositories\Impl;

use App\Repositories\AvisRepositoryInterface;
use App\Entities\Models\Avis;
use App\Core\Database;
use PDO;

class AvisRepository implements AvisRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(Avis $avis): int
    {
        $sql = "INSERT INTO avis (id_reservation, rating, comment) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $avis->getIdReservation(),
            $avis->getRating(),
            $avis->getComment()
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByLogement(int $logementId): array
    {
        // Join with Reservation to filter by Logement, and Users to get Author Name
        $sql = "SELECT a.*, u.firstname as author_name 
                FROM avis a
                JOIN reservation r ON a.id_reservation = r.id
                JOIN users u ON r.id_user = u.id
                WHERE r.id_log = ?
                ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return Avis::fromArray($row);
        }, $results);
    }

    public function getAverageRating(int $logementId): float
    {
        $sql = "SELECT AVG(a.rating) as avg_rating
                FROM avis a
                JOIN reservation r ON a.id_reservation = r.id
                WHERE r.id_log = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['avg_rating'] ? (float) $result['avg_rating'] : 0.0;
    }
}
