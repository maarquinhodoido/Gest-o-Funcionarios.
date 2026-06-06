<?php

namespace App\Application\Services;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\EquipmentRepositoryInterface;
use App\Domain\Repositories\EquipmentAssignmentRepositoryInterface;
use App\Domain\Repositories\AuditLogRepositoryInterface;

class DashboardService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EquipmentRepositoryInterface $equipmentRepository,
        private EquipmentAssignmentRepositoryInterface $assignmentRepository,
        private AuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function getStats(int $companyId): array
    {
        return [
            'active_users' => count($this->userRepository->findActiveByCompany($companyId)),
            'total_users' => $this->userRepository->countByCompany($companyId),
            'equipment_assigned' => $this->assignmentRepository->countActiveByCompany($companyId),
            'equipment_maintenance' => $this->equipmentRepository->countByStatus($companyId, 'maintenance'),
            'equipment_lost' => $this->equipmentRepository->countByStatus($companyId, 'lost'),
            'equipment_available' => $this->equipmentRepository->countByStatus($companyId, 'available'),
            'total_equipment' => $this->equipmentRepository->countByCompany($companyId),
            'failed_logins' => count($this->auditLogRepository->findFailedLogins($companyId, 1440)),
            'critical_alerts' => count($this->auditLogRepository->findSuspiciousActivities($companyId)),
        ];
    }

    public function getRecentActivities(int $companyId, int $limit = 10): array
    {
        return $this->auditLogRepository->findRecent($companyId, $limit);
    }

    public function getRecentLogins(int $companyId, int $limit = 10): array
    {
        $logs = $this->auditLogRepository->findAll($companyId, [
            'action' => 'login',
            'limit' => $limit,
        ]);
        return $logs;
    }
}
