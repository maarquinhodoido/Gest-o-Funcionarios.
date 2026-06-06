<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Notification;
use App\Domain\Repositories\NotificationRepositoryInterface;
use App\Infrastructure\Models\NotificationModel;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification
    {
        $model = NotificationModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByUser(int $userId, array $filters = []): array
    {
        $query = NotificationModel::where('user_id', $userId);
        if (!empty($filters['is_read'])) {
            $query->where('is_read', $filters['is_read'] === 'true' || $filters['is_read'] === true);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        $perPage = $filters['per_page'] ?? 15;
        $models = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return [
            'items' => array_map(fn($m) => $this->toDomain($m), $models->items()),
            'total' => $models->total(),
            'page' => $models->currentPage(),
            'per_page' => $models->perPage(),
            'last_page' => $models->lastPage(),
        ];
    }

    public function findByCompany(int $companyId, array $filters = []): array
    {
        $query = NotificationModel::where('company_id', $companyId);
        $perPage = $filters['per_page'] ?? 15;
        $models = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return [
            'items' => array_map(fn($m) => $this->toDomain($m), $models->items()),
            'total' => $models->total(),
            'page' => $models->currentPage(),
            'per_page' => $models->perPage(),
            'last_page' => $models->lastPage(),
        ];
    }

    public function save(Notification $notification): Notification
    {
        $model = $this->toModel($notification);
        $model->save();
        return $this->toDomain($model);
    }

    public function countUnread(int $userId): int
    {
        return NotificationModel::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAllAsRead(int $userId): void
    {
        NotificationModel::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function delete(int $id): void
    {
        NotificationModel::findOrFail($id)->delete();
    }

    private function toDomain(NotificationModel $model): Notification
    {
        return new Notification(
            id: $model->id,
            companyId: $model->company_id,
            userId: $model->user_id,
            type: $model->type,
            title: $model->title,
            message: $model->message,
            data: $model->data,
            isRead: $model->is_read,
            readAt: $model->read_at ? new \DateTimeImmutable($model->read_at) : null,
            createdAt: new \DateTimeImmutable($model->created_at),
        );
    }

    private function toModel(Notification $notification): NotificationModel
    {
        $model = new NotificationModel();
        $model->fill([
            'company_id' => $notification->getCompanyId(),
            'user_id' => $notification->getUserId(),
            'type' => $notification->getType(),
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'data' => $notification->getData(),
            'is_read' => $notification->isRead(),
            'read_at' => $notification->getReadAt()?->format('Y-m-d H:i:s'),
            'created_at' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
        return $model;
    }
}
