<?php

namespace App\Application\Services;

use App\Domain\Entities\Notification;
use App\Domain\Repositories\NotificationRepositoryInterface;

class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function create(
        int $companyId,
        string $title,
        string $message,
        string $type = Notification::TYPE_INFO,
        ?int $userId = null,
        ?array $data = null,
    ): Notification {
        $notification = new Notification(
            id: null,
            companyId: $companyId,
            userId: $userId,
            type: $type,
            title: $title,
            message: $message,
            data: $data,
        );

        return $this->notificationRepository->save($notification);
    }

    public function notifyUser(int $userId, string $title, string $message, string $type = Notification::TYPE_INFO, ?array $data = null): Notification
    {
        $user = app(\App\Domain\Repositories\UserRepositoryInterface::class)->findById($userId);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        return $this->create(
            companyId: $user->getCompanyId(),
            userId: $userId,
            title: $title,
            message: $message,
            type: $type,
            data: $data,
        );
    }

    public function findByUser(int $userId, array $filters = []): array
    {
        return $this->notificationRepository->findByUser($userId, $filters);
    }

    public function findByCompany(int $companyId, array $filters = []): array
    {
        return $this->notificationRepository->findByCompany($companyId, $filters);
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->notificationRepository->save($notification);
        }
    }

    public function markAllAsRead(int $userId): void
    {
        $this->notificationRepository->markAllAsRead($userId);
    }

    public function countUnread(int $userId): int
    {
        return $this->notificationRepository->countUnread($userId);
    }

    public function notifyOverdueEquipment(array $assignments): void
    {
        foreach ($assignments as $assignment) {
            $this->notifyUser(
                userId: $assignment->getUserId(),
                title: 'Equipment Return Overdue',
                message: 'Your assigned equipment is overdue for return.',
                type: Notification::TYPE_WARNING,
            );
        }
    }

    public function notifySuspiciousLogin(int $companyId, int $userId): void
    {
        $this->create(
            companyId: $companyId,
            userId: $userId,
            title: 'Suspicious Login Detected',
            message: 'We detected a login from an unfamiliar location or device.',
            type: Notification::TYPE_ALERT,
        );
    }
}
