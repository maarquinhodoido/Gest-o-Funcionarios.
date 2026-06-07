<?php

namespace App\Application\Services;

use App\Domain\Entities\AuditLog;
use App\Domain\Repositories\AuditLogRepositoryInterface;
use Illuminate\Http\Request;

class AuditService
{
    public function __construct(
        private AuditLogRepositoryInterface $auditLogRepository,
        private ?Request $request = null,
    ) {}

    public function log(
        int $companyId,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        string $severity = AuditLog::SEVERITY_INFO,
        ?string $description = null,
    ): AuditLog {
        $ip = $this->request?->ip() ?? '';
        $userAgent = $this->request?->userAgent();

        if ($userId === null && ($user = auth('api')->user())) {
            $userId = $user->id;
        }

        $log = new AuditLog(
            id: null,
            companyId: $companyId,
            userId: $userId,
            action: $action,
            entityType: $entityType,
            entityId: $entityId,
            oldValues: $oldValues,
            newValues: $newValues,
            ipAddress: $ip,
            userAgent: $userAgent,
            severity: $severity,
            description: $description,
        );

        return $this->auditLogRepository->save($log);
    }

    public function logLogin(int $companyId, int $userId, string $ip, ?string $userAgent): AuditLog
    {
        return $this->log(
            companyId: $companyId,
            userId: $userId,
            action: AuditLog::ACTION_LOGIN,
            entityType: 'auth',
            description: "User login",
            newValues: ['ip' => $ip, 'user_agent' => $userAgent],
        );
    }

    public function logFailedLogin(int $companyId, ?int $userId, string $ip, ?string $userAgent): AuditLog
    {
        return $this->log(
            companyId: $companyId,
            userId: $userId,
            action: AuditLog::ACTION_LOGIN_FAILED,
            entityType: 'auth',
            severity: AuditLog::SEVERITY_WARNING,
            description: "Failed login attempt",
            newValues: ['ip' => $ip, 'user_agent' => $userAgent],
        );
    }

    public function logLogout(int $companyId, int $userId): AuditLog
    {
        return $this->log(
            companyId: $companyId,
            userId: $userId,
            action: AuditLog::ACTION_LOGOUT,
            entityType: 'auth',
            description: "User logout",
        );
    }

    public function findRecent(int $companyId, int $limit = 20): array
    {
        return $this->auditLogRepository->findRecent($companyId, $limit);
    }

    public function findSuspiciousActivities(int $companyId): array
    {
        return $this->auditLogRepository->findSuspiciousActivities($companyId);
    }

    public function findFailedLogins(int $companyId, int $minutes = 30): array
    {
        return $this->auditLogRepository->findFailedLogins($companyId, $minutes);
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        return $this->auditLogRepository->findAll($companyId, $filters);
    }
}
