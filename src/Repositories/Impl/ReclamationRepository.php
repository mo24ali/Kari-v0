<?php

namespace App\Repositories\Impl;


use App\core\Database;

use PDO;
use function App\dump_die;

class ReclamationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $userId, int $logementId, string $message): bool
    {
        $sql = "INSERT INTO reclamations (id_user, id_log, message, created_at) 
                        VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        // dump_die($stmt->execute([$userId, $logementId, $message]));
        return $stmt->execute([$userId, $logementId, $message]);
    }

    public function findByHost(int $hostId): array
    {
        // dump_die($hostId);
        $sql = "SELECT r.*, u.firstname, u.lastname, l.address, l.price 
                FROM reclamations r
                JOIN logement l ON r.id_log = l.id
                JOIN users u ON r.id_user = u.id
                WHERE l.id_owner = ?";
                // ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hostId]);
        // dump_die($stmt->fetch(PDO::FETCH_ASSOC));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $sql = "SELECT r.*, u.firstname, u.lastname, l.address, 
                        o.firstname as owner_firstname, 
                        o.lastname as owner_lastname
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
