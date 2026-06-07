<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\AuditLog;
use App\Domain\Repositories\AuditLogRepositoryInterface;
use App\Infrastructure\Models\AuditLogModel;
use Carbon\Carbon;

class EloquentAuditLogRepository implements AuditLogRepositoryInterface
{
    public function findById(int $id): ?AuditLog
    {
        $model = AuditLogModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        $query = AuditLogModel::where('company_id', $companyId);

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (!empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }
        if (!empty($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        $perPage = $filters['per_page'] ?? 25;
        $models = $query->with('user')->orderBy('created_at', 'desc')->paginate($perPage);

        return [
            'items' => array_map(fn($m) => $this->toDomain($m), $models->items()),
            'total' => $models->total(),
            'page' => $models->currentPage(),
            'per_page' => $models->perPage(),
            'last_page' => $models->lastPage(),
        ];
    }

    public function save(AuditLog $log): AuditLog
    {
        $model = new AuditLogModel();
        $model->fill($this->toArray($log));
        $model->save();
        return $this->toDomain($model);
    }

    public function findRecent(int $companyId, int $limit = 20): array
    {
        return AuditLogModel::where('company_id', $companyId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findFailedLogins(int $companyId, int $minutes = 30): array
    {
        $since = Carbon::now()->subMinutes($minutes);
        return AuditLogModel::where('company_id', $companyId)
            ->where('action', AuditLog::ACTION_LOGIN_FAILED)
            ->where('created_at', '>=', $since->toDateTimeString())
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findSuspiciousActivities(int $companyId): array
    {
        $since = Carbon::now()->subDays(7);
        return AuditLogModel::where('company_id', $companyId)
            ->where('severity', AuditLog::SEVERITY_CRITICAL)
            ->where('created_at', '>=', $since->toDateTimeString())
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function countByAction(int $companyId, string $action): int
    {
        return AuditLogModel::where('company_id', $companyId)
            ->where('action', $action)
            ->count();
    }

    private function toDomain(AuditLogModel $model): AuditLog
    {
        return new AuditLog(
            id: $model->id,
            companyId: $model->company_id,
            userId: $model->user_id,
            action: $model->action,
            entityType: $model->entity_type,
            entityId: $model->entity_id,
            oldValues: $model->old_values,
            newValues: $model->new_values,
            ipAddress: $model->ip_address ?? '',
            userAgent: $model->user_agent,
            location: $model->location,
            severity: $model->severity,
            description: $model->description,
            userName: $model->relationLoaded('user') && $model->user ? $model->user->name : null,
            createdAt: new \DateTimeImmutable($model->created_at),
        );
    }

    private function toArray(AuditLog $log): array
    {
        return [
            'company_id' => $log->getCompanyId(),
            'user_id' => $log->getUserId(),
            'action' => $log->getAction(),
            'entity_type' => $log->getEntityType(),
            'entity_id' => $log->getEntityId(),
            'old_values' => $log->getOldValues(),
            'new_values' => $log->getNewValues(),
            'ip_address' => $log->getIpAddress(),
            'user_agent' => $log->getUserAgent(),
            'location' => $log->getLocation(),
            'severity' => $log->getSeverity(),
            'description' => $log->getDescription(),
            'created_at' => $log->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
