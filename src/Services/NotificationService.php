<?php

namespace App\Services;

use App\Repositories\NotificationRepositoryInterface;
use App\Entities\Models\Notification;

class NotificationService
{
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function notifyUser(int $userId, string $type, string $message): Notification
    {
        $notification = new Notification($userId, $type, $message);
        $this->notificationRepository->save($notification);
        return $notification;
    }

    public function getUserNotifications(int $userId): array
    {
        return $this->notificationRepository->findByUser($userId);
    }

    public function markAsRead(int $id): bool
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->countUnread($userId);
    }
}
