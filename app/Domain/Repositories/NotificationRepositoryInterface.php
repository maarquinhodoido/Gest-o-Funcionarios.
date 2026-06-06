<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Notification;

interface NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification;
    public function findByUser(int $userId, array $filters = []): array;
    public function findByCompany(int $companyId, array $filters = []): array;
    public function save(Notification $notification): Notification;
    public function countUnread(int $userId): int;
    public function markAllAsRead(int $userId): void;
}
