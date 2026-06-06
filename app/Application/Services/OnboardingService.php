<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateUserDTO;
use App\Domain\Entities\AuditLog;

class OnboardingService
{
    public function __construct(
        private UserService $userService,
        private EquipmentAssignmentService $assignmentService,
        private NotificationService $notificationService,
        private AuditService $auditService,
    ) {}

    public function onboard(CreateUserDTO $dto, array $equipmentIds = [], array $roleNames = []): array
    {
        $user = $this->userService->create($dto);

        $assignments = [];
        foreach ($equipmentIds as $equipmentId) {
            $assignDto = new \App\Application\DTOs\AssignEquipmentDTO(
                equipmentId: $equipmentId,
                userId: $user->getId(),
                assignedBy: $dto->companyId,
                notes: 'Auto-assigned during onboarding',
            );
            $assignments[] = $this->assignmentService->assign($assignDto);
        }

        foreach ($roleNames as $roleName) {
            app(RoleService::class)->assignRoleToUser(
                userId: $user->getId(),
                roleName: $roleName,
                companyId: $dto->companyId,
            );
        }

        $this->notificationService->create(
            companyId: $dto->companyId,
            userId: $user->getId(),
            title: 'Welcome to the company!',
            message: 'Your account has been created successfully. Your equipment has been assigned.',
            type: 'success',
        );

        $this->auditService->log(
            companyId: $dto->companyId,
            userId: $user->getId(),
            action: AuditLog::ACTION_CREATE,
            entityType: 'onboarding',
            entityId: $user->getId(),
            description: "Onboarding completed for user: {$user->getName()}",
        );

        return [
            'user' => $user,
            'assignments' => $assignments,
        ];
    }

    public function offboard(int $userId): void
    {
        $user = $this->userService->findById($userId);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $activeAssignments = $this->assignmentService->findActiveByUser($userId);
        foreach ($activeAssignments as $assignment) {
            $this->assignmentService->return($assignment->getId(), $userId);
        }

        $this->userService->deactivate($userId);

        $this->notificationService->create(
            companyId: $user->getCompanyId(),
            userId: $userId,
            title: 'Account Deactivated',
            message: 'Your account has been deactivated. Please return all equipment.',
            type: 'warning',
        );

        $this->auditService->log(
            companyId: $user->getCompanyId(),
            userId: $userId,
            action: AuditLog::ACTION_UPDATE,
            entityType: 'offboarding',
            entityId: $userId,
            severity: AuditLog::SEVERITY_WARNING,
            description: "Offboarding completed for user: {$user->getName()}",
        );
    }
}
