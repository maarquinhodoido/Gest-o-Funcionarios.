<?php

namespace App\Application\Services;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\DepartmentRepositoryInterface;
use App\Domain\Repositories\EquipmentRepositoryInterface;
use App\Domain\Repositories\EquipmentAssignmentRepositoryInterface;
use App\Domain\Repositories\AuditLogRepositoryInterface;

class DashboardService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private EquipmentRepositoryInterface $equipmentRepository,
        private EquipmentAssignmentRepositoryInterface $assignmentRepository,
        private AuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function getStats(int $companyId): array
    {
        $totalUsers = $this->userRepository->countByCompany($companyId);
        $activeUsers = count($this->userRepository->findActiveByCompany($companyId));
        $totalEquipment = $this->equipmentRepository->countByCompany($companyId);
        $assigned = $this->assignmentRepository->countActiveByCompany($companyId);
        $available = $this->equipmentRepository->countByStatus($companyId, 'available');
        $maintenance = $this->equipmentRepository->countByStatus($companyId, 'maintenance');
        $lost = $this->equipmentRepository->countByStatus($companyId, 'lost');
        $blockedUsers = count($this->userRepository->findAll($companyId, ['status' => 'blocked'])['items'] ?? []);
        $totalDepartments = $this->departmentRepository->countByCompany($companyId);

        $failedLogins = 0;
        $criticalAlerts = 0;
        try {
            $failedLogins = count($this->auditLogRepository->findFailedLogins($companyId, 1440));
            $criticalAlerts = count($this->auditLogRepository->findSuspiciousActivities($companyId));
        } catch (\Exception $e) {
            report($e);
        }

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'blocked_users' => $blockedUsers,
            'total_departments' => $totalDepartments,
            'total_equipment' => $totalEquipment,
            'equipment_available' => $available,
            'equipment_assigned' => $assigned,
            'equipment_maintenance' => $maintenance,
            'equipment_lost' => $lost,
            'usage_percentage' => $totalEquipment > 0 ? round(($assigned / $totalEquipment) * 100) : 0,
            'available_percentage' => $totalEquipment > 0 ? round(($available / $totalEquipment) * 100) : 0,
            'failed_logins' => $failedLogins,
            'critical_alerts' => $criticalAlerts,
        ];
    }

    public function getRecentActivities(int $companyId, int $limit = 10): array
    {
        try {
            return $this->auditLogRepository->findRecent($companyId, $limit);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
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
