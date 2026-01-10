<?php

namespace App\Repositories\Impl;

use App\Repositories\NotificationRepositoryInterface;
use App\Entities\Models\Notification;
use App\Core\Database;
use PDO;

class NotificationRepository implements NotificationRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(Notification $notification): int
    {
        $sql = "INSERT INTO notifications (id_user, type, message, is_read) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $notification->getIdUser(),
            $notification->getType(),
            $notification->getMessage(),
            $notification->isRead() ? 1 : 0
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByUser(int $userId): array
    {
        $sql = "SELECT * FROM notifications WHERE id_user = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => Notification::fromArray($row), $results);
    }

    public function markAsRead(int $id): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function countUnread(int $userId): int
    {
        $sql = "SELECT COUNT(*) as total FROM notifications WHERE id_user = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['total'];
    }

    public function markAllReadByUser(int $userId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id_user = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
