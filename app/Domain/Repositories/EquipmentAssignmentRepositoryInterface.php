<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\EquipmentAssignment;

interface EquipmentAssignmentRepositoryInterface
{
    public function findById(int $id): ?EquipmentAssignment;
    public function findActiveByEquipment(int $equipmentId): ?EquipmentAssignment;
    public function findActiveByUser(int $userId): array;
    public function findAllByCompany(int $companyId, array $filters = []): array;
    public function save(EquipmentAssignment $assignment): EquipmentAssignment;
    public function update(EquipmentAssignment $assignment): EquipmentAssignment;
    public function findHistoryByEquipment(int $equipmentId): array;
    public function findHistoryByUser(int $userId): array;
    public function countActiveByCompany(int $companyId): int;
    public function delete(int $id): void;
}
