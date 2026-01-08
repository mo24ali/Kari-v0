<?php

namespace App\Repositories\Impl;

use App\core\Database;
use App\Entities\Models\Logement;
use PDO;

class LogementRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    public function findAll(): array
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email,
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                ORDER BY l.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findById(int $id): ?Logement
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email 
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                WHERE l.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? Logement::fromArray($result) : null;
    }
    public function findByIdAsArray(int $id): ?array
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email,
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                WHERE l.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    public function findByOwner(int $ownerId): array
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email,
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                WHERE l.id_owner = ? 
                ORDER BY l.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function save(Logement $logement): int
    {
        $sql = "INSERT INTO logement (id_owner, price, address) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $logement->getIdOwner(),
            $logement->getPrice(),
            $logement->getAddress()
        ]);
        $id = (int) $this->db->lastInsertId();
        $logement->setId($id);
        return $id;
    }
    public function update(Logement $logement): bool
    {
        if ($logement->getId() === null) {
            return false;
        }

        $sql = "UPDATE logement SET id_owner = ?, price = ?, address = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $logement->getIdOwner(),
            $logement->getPrice(),
            $logement->getAddress(),
            $logement->getId()
        ]);
    }
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM logement WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM logement";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
    // search methods for filters
    public function searchByAddress(string $searchTerm): array
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email,
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                WHERE l.address LIKE ? 
                ORDER BY l.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['%' . $searchTerm . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByPriceRange(float $minPrice, float $maxPrice): array
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email,
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                WHERE l.price >= ? AND l.price <= ? 
                ORDER BY l.price ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$minPrice, $maxPrice]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(array $filters): array
    {
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email as owner_email,
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM logement l 
                LEFT JOIN users u ON l.id_owner = u.id 
                WHERE 1=1";

        $params = [];

        if (!empty($filters['destination'])) {
            $sql .= " AND l.address LIKE ?";
            $params[] = '%' . $filters['destination'] . '%';
        }

        if (!empty($filters['check_in']) && !empty($filters['check_out'])) {
            
            $sql .= " AND l.id NOT IN (
                SELECT id_log FROM reservation 
                WHERE start_date < ? AND end_date > ?
            )";
            $startDate = $filters['check_in'];
            $endDate = $filters['check_out'];

            $params[] = $endDate;
            $params[] = $startDate;
        }

        $sql .= " ORDER BY l.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservedDates(int $logementId): array
    {
        $sql = "SELECT start_date, end_date FROM reservation WHERE id_log = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
