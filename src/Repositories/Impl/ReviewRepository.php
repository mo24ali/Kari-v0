<?php

namespace App\Repositories\Impl;


use App\core\Database;
use PDO;

use function App\dump_die;

class ReviewRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $userId, int $logementId, string $message): bool
    {
        $sql = "INSERT INTO review (contenu, id_writer, id_log, data_publication) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $logementId, $message]);
    }

    public function findByHost(int $hostId): array
    {
        // dump_die($hostId);
        $sql = "SELECT r.*, u.firstname, u.lastname, l.address, l.price 
                FROM review r
                JOIN logement l ON r.id_log = l.id
                JOIN users u ON r.id_writer = u.id
                WHERE l.id_owner = ?";
                // ORDER BY r.data_publication DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hostId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $sql = "SELECT r.*, u.firstname, u.lastname, l.address, o.firstname as owner_firstname, o.lastname as owner_lastname
                FROM review r
                JOIN logement l ON r.id_log = l.id
                JOIN users u ON r.id_user = u.id
                JOIN users o ON l.id_owner = o.id
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
