<?php

namespace App\Repositories\Impl;

use App\core\Database;
use PDO;
use PDOException;
class FavorisRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add(int $userId, int $logementId): bool
    {
        $sql = "INSERT INTO favoris (id_voy, id_log) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$userId, $logementId]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function remove(int $userId, int $logementId): bool
    {
        $sql = "DELETE FROM favoris WHERE id_voy = ? AND id_log = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $logementId]);
    }

    public function findByUser(int $userId): array
    {
        $sql = "SELECT l.*, 
                (SELECT image_path FROM images WHERE id_logement = l.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM favoris f
                JOIN logement l ON f.id_log = l.id
                WHERE f.id_voy = ?
                ORDER BY f.id DESC"; 

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isFavoris(int $userId, int $logementId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM favoris WHERE id_voy = ? AND id_log = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $logementId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'] > 0;
    }
}
