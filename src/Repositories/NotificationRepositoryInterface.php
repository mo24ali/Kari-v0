<?php

namespace App\Repositories;

use App\Entities\Models\Notification;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): int;
    public function findByUser(int $userId): array;
    public function markAsRead(int $id): bool;
    public function countUnread(int $userId): int;
}
