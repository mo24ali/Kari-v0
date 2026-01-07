<?php

namespace App\Repositories;

use App\core\Database;
use PDO;

class ReclamationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $userId, int $logementId, string $message): bool
    {
        $sql = "INSERT INTO reclamation (id_user, id_log, message, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $logementId, $message]);
    }

    public function findByHost(int $hostId): array
    {
        $sql = "SELECT r.*, u.firstname, u.lastname, l.address, l.price 
                FROM reclamation r
                JOIN logement l ON r.id_log = l.id
                JOIN users u ON r.id_user = u.id
                WHERE l.id_owner = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hostId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $sql = "SELECT r.*, u.firstname, u.lastname, l.address, o.firstname as owner_firstname, o.lastname as owner_lastname
                FROM reclamation r
                JOIN logement l ON r.id_log = l.id
                JOIN users u ON r.id_user = u.id
                JOIN users o ON l.id_owner = o.id
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
