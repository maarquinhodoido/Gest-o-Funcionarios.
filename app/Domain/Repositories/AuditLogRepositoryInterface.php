<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\AuditLog;

interface AuditLogRepositoryInterface
{
    public function findById(int $id): ?AuditLog;
    public function findAll(int $companyId, array $filters = []): array;
    public function save(AuditLog $log): AuditLog;
    public function findRecent(int $companyId, int $limit = 20): array;
    public function findFailedLogins(int $companyId, int $minutes = 30): array;
    public function findSuspiciousActivities(int $companyId): array;
    public function countByAction(int $companyId, string $action): int;
}
