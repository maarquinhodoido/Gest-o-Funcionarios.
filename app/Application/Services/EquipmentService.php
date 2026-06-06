<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateEquipmentDTO;
use App\Domain\Entities\Equipment;
use App\Domain\Entities\AuditLog;
use App\Domain\Repositories\EquipmentRepositoryInterface;
use App\Domain\Repositories\EquipmentAssignmentRepositoryInterface;

class EquipmentService
{
    public function __construct(
        private EquipmentRepositoryInterface $equipmentRepository,
        private EquipmentAssignmentRepositoryInterface $assignmentRepository,
        private AuditService $auditService,
    ) {}

    public function create(CreateEquipmentDTO $dto): Equipment
    {
        $existing = $this->equipmentRepository->findBySerialNumber($dto->serialNumber, $dto->companyId);
        if ($existing) {
            throw new \DomainException('Equipment with this serial number already exists.');
        }

        $equipment = new Equipment(
            companyId: $dto->companyId,
            equipmentTypeId: $dto->equipmentTypeId,
            serialNumber: $dto->serialNumber,
            brand: $dto->brand,
            model: $dto->model,
            location: $dto->location,
            warrantyEnd: $dto->warrantyEnd ? new \DateTimeImmutable($dto->warrantyEnd) : null,
            purchaseDate: $dto->purchaseDate ? new \DateTimeImmutable($dto->purchaseDate) : null,
            purchasePrice: $dto->purchasePrice,
            supplier: $dto->supplier,
            notes: $dto->notes,
        );

        $saved = $this->equipmentRepository->save($equipment);

        $this->auditService->log(
            companyId: $dto->companyId,
            action: AuditLog::ACTION_CREATE,
            entityType: 'equipment',
            entityId: $saved->getId(),
            description: "Equipment created: {$saved->getBrand()} {$saved->getModel()} ({$saved->getSerialNumber()})",
        );

        return $saved;
    }

    public function findById(int $id): ?Equipment
    {
        return $this->equipmentRepository->findById($id);
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        return $this->equipmentRepository->findAll($companyId, $filters);
    }

    public function update(int $id, array $data): Equipment
    {
        $equipment = $this->equipmentRepository->findById($id);
        if (!$equipment) {
            throw new \DomainException('Equipment not found.');
        }

        $saved = $this->equipmentRepository->update($equipment);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            action: AuditLog::ACTION_UPDATE,
            entityType: 'equipment',
            entityId: $id,
            newValues: $data,
            description: "Equipment updated: {$saved->getBrand()} {$saved->getModel()}",
        );

        return $saved;
    }

    public function markMaintenance(int $id): Equipment
    {
        $equipment = $this->equipmentRepository->findById($id);
        if (!$equipment) {
            throw new \DomainException('Equipment not found.');
        }

        $equipment->markMaintenance();
        $saved = $this->equipmentRepository->update($equipment);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            action: AuditLog::ACTION_UPDATE,
            entityType: 'equipment',
            entityId: $id,
            description: "Equipment marked as maintenance: {$saved->getSerialNumber()}",
        );

        return $saved;
    }

    public function markLost(int $id): Equipment
    {
        $equipment = $this->equipmentRepository->findById($id);
        if (!$equipment) {
            throw new \DomainException('Equipment not found.');
        }

        $equipment->markLost();
        $saved = $this->equipmentRepository->update($equipment);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            action: AuditLog::ACTION_UPDATE,
            entityType: 'equipment',
            entityId: $id,
            severity: AuditLog::SEVERITY_CRITICAL,
            description: "Equipment marked as lost: {$saved->getSerialNumber()}",
        );

        return $saved;
    }

    public function delete(int $id): void
    {
        $equipment = $this->equipmentRepository->findById($id);
        if (!$equipment) {
            throw new \DomainException('Equipment not found.');
        }

        $activeAssignment = $this->assignmentRepository->findActiveByEquipment($id);
        if ($activeAssignment) {
            throw new \DomainException('Cannot delete equipment that is currently assigned.');
        }

        $this->equipmentRepository->delete($id);

        $this->auditService->log(
            companyId: $equipment->getCompanyId(),
            action: AuditLog::ACTION_DELETE,
            entityType: 'equipment',
            entityId: $id,
            description: "Equipment deleted: {$equipment->getSerialNumber()}",
        );
    }

    public function getStats(int $companyId): array
    {
        return [
            'total' => $this->equipmentRepository->countByCompany($companyId),
            'available' => $this->equipmentRepository->countByStatus($companyId, Equipment::STATUS_AVAILABLE),
            'assigned' => $this->equipmentRepository->countByStatus($companyId, Equipment::STATUS_ASSIGNED),
            'maintenance' => $this->equipmentRepository->countByStatus($companyId, Equipment::STATUS_MAINTENANCE),
            'lost' => $this->equipmentRepository->countByStatus($companyId, Equipment::STATUS_LOST),
            'disabled' => $this->equipmentRepository->countByStatus($companyId, Equipment::STATUS_DISABLED),
        ];
    }

    public function findAvailable(int $companyId): array
    {
        return $this->equipmentRepository->findAvailable($companyId);
    }
}
