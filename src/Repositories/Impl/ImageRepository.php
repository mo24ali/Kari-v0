<?php

namespace App\Repositories\Impl;

use App\core\Database;
use PDO;

class ImageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(array $data): int
    {
        $sql = "INSERT INTO images (id_logement, image_path, is_primary) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['id_logement'],
            $data['image_path'],
            $data['is_primary'] ?? 0
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByLogement(int $logementId): array
    {
        $sql = "SELECT * FROM images WHERE id_logement = ? ORDER BY is_primary DESC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPrimary(int $logementId): ?array
    {
        $sql = "SELECT * FROM images WHERE id_logement = ? AND is_primary = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM images WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function deleteByLogement(int $logementId): bool
    {
        $sql = "DELETE FROM images WHERE id_logement = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$logementId]);
    }

    public function setPrimary(int $id, int $logementId): bool
    {
        $sql = "UPDATE images SET is_primary = 0 WHERE id_logement = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$logementId]);

        $sql = "UPDATE images SET is_primary = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function findByLogementIds(array $logementIds): array
    {
        if (empty($logementIds)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($logementIds) - 1) . '?';
        $sql = "SELECT * FROM images WHERE id_logement IN ($placeholders) ORDER BY is_primary DESC, id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($logementIds);

        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by logement_id
        $grouped = [];
        foreach ($images as $img) {
            $grouped[$img['id_logement']][] = $img;
        }

        return $grouped;
    }
}
