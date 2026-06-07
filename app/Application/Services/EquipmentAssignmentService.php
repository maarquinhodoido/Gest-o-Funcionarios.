<?php

namespace App\Application\Services;

use App\Application\DTOs\AssignEquipmentDTO;
use App\Domain\Entities\EquipmentAssignment;
use App\Domain\Entities\AuditLog;
use App\Domain\Repositories\EquipmentRepositoryInterface;
use App\Domain\Repositories\EquipmentAssignmentRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;

class EquipmentAssignmentService
{
    public function __construct(
        private EquipmentAssignmentRepositoryInterface $assignmentRepository,
        private EquipmentRepositoryInterface $equipmentRepository,
        private UserRepositoryInterface $userRepository,
        private AuditService $auditService,
        private NotificationService $notificationService,
    ) {}

    public function assign(AssignEquipmentDTO $dto): EquipmentAssignment
    {
        $equipment = $this->equipmentRepository->findById($dto->equipmentId);
        if (!$equipment) {
            throw new \DomainException('Equipment not found.');
        }

        if (!$equipment->isAvailable()) {
            throw new \DomainException('Equipment is not available for assignment.');
        }

        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $assignment = new EquipmentAssignment(
            id: null,
            equipmentId: $dto->equipmentId,
            userId: $dto->userId,
            assignedBy: $dto->assignedBy,
            notes: $dto->notes,
            expectedReturnAt: $dto->expectedReturnAt ? new \DateTimeImmutable($dto->expectedReturnAt) : null,
            reference: ReferenceGenerator::generate('equipment_assignment'),
        );

        if ($dto->responsibilityTerm) {
            $assignment->addResponsibilityTerm($dto->responsibilityTerm);
        }

        $saved = $this->assignmentRepository->save($assignment);
        $equipment->assign();
        $this->equipmentRepository->update($equipment);

        $this->auditService->log(
            companyId: $equipment->getCompanyId(),
            userId: $dto->assignedBy,
            action: AuditLog::ACTION_ASSIGN,
            entityType: 'equipment_assignment',
            entityId: $saved->getId(),
            description: "Equipment {$equipment->getSerialNumber()} assigned to {$user->getName()}",
        );

        $this->notificationService->create(
            companyId: $equipment->getCompanyId(),
            title: 'Equipamento Atribuído',
            message: "Equipamento {$equipment->getSerialNumber()} foi atribuído a {$user->getName()}",
            type: \App\Domain\Entities\Notification::TYPE_SUCCESS,
        );

        return $saved;
    }

    public function return(int $assignmentId, int $returnedBy): EquipmentAssignment
    {
        $assignment = $this->assignmentRepository->findById($assignmentId);
        if (!$assignment) {
            throw new \DomainException('Assignment not found.');
        }

        if (!$assignment->isActive()) {
            throw new \DomainException('Assignment is already closed.');
        }

        $assignment->return($returnedBy);
        $saved = $this->assignmentRepository->update($assignment);

        $equipment = $this->equipmentRepository->findById($assignment->getEquipmentId());
        if ($equipment) {
            $equipment->markAvailable();
            $this->equipmentRepository->update($equipment);
        }

        $user = $this->userRepository->findById($assignment->getUserId());

        $this->auditService->log(
            companyId: $equipment?->getCompanyId() ?? 0,
            userId: $returnedBy,
            action: AuditLog::ACTION_RETURN,
            entityType: 'equipment_assignment',
            entityId: $assignmentId,
            description: "Equipment {$equipment?->getSerialNumber()} returned by {$user?->getName()}",
        );

        $this->notificationService->create(
            companyId: $equipment?->getCompanyId() ?? 0,
            title: 'Equipamento Devolvido',
            message: "Equipamento {$equipment?->getSerialNumber()} foi devolvido por {$user?->getName()}",
            type: \App\Domain\Entities\Notification::TYPE_SUCCESS,
        );

        return $saved;
    }

    public function markLost(int $assignmentId): EquipmentAssignment
    {
        $assignment = $this->assignmentRepository->findById($assignmentId);
        if (!$assignment) {
            throw new \DomainException('Assignment not found.');
        }

        $assignment->markLost();
        $saved = $this->assignmentRepository->update($assignment);

        $equipment = $this->equipmentRepository->findById($assignment->getEquipmentId());
        if ($equipment) {
            $equipment->markLost();
            $this->equipmentRepository->update($equipment);
        }

        $this->auditService->log(
            companyId: $equipment?->getCompanyId() ?? 0,
            action: AuditLog::ACTION_UPDATE,
            entityType: 'equipment_assignment',
            entityId: $assignmentId,
            severity: AuditLog::SEVERITY_CRITICAL,
            description: "Equipment {$equipment?->getSerialNumber()} marked as lost during assignment",
        );

        return $saved;
    }

    public function findActiveByUser(int $userId): array
    {
        return $this->assignmentRepository->findActiveByUser($userId);
    }

    public function findAllByCompany(int $companyId, array $filters = []): array
    {
        return $this->assignmentRepository->findAllByCompany($companyId, $filters);
    }

    public function findHistoryByEquipment(int $equipmentId): array
    {
        return $this->assignmentRepository->findHistoryByEquipment($equipmentId);
    }

    public function findHistoryByUser(int $userId): array
    {
        return $this->assignmentRepository->findHistoryByUser($userId);
    }

    public function countActiveByCompany(int $companyId): int
    {
        return $this->assignmentRepository->countActiveByCompany($companyId);
    }

    public function findById(int $id): ?EquipmentAssignment
    {
        return $this->assignmentRepository->findById($id);
    }

    public function delete(int $id): void
    {
        $assignment = $this->assignmentRepository->findById($id);
        if (!$assignment) {
            throw new \DomainException('Assignment not found.');
        }

        $equipment = $this->equipmentRepository->findById($assignment->getEquipmentId());

        $this->auditService->log(
            companyId: $equipment?->getCompanyId() ?? 0,
            action: AuditLog::ACTION_DELETE,
            entityType: 'equipment_assignment',
            entityId: $id,
            description: "Assignment deleted: equipment #{$assignment->getEquipmentId()} / user #{$assignment->getUserId()}",
        );

        $this->notificationService->create(
            companyId: $equipment?->getCompanyId() ?? 0,
            title: 'Atribuição Eliminada',
            message: "Atribuição do equipamento #{$assignment->getEquipmentId()} foi eliminada",
            type: \App\Domain\Entities\Notification::TYPE_WARNING,
        );

        $this->assignmentRepository->delete($id);
    }
}
