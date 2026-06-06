<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Equipment;

interface EquipmentRepositoryInterface
{
    public function findById(int $id): ?Equipment;
    public function findBySerialNumber(string $serial, int $companyId): ?Equipment;
    public function findAll(int $companyId, array $filters = []): array;
    public function save(Equipment $equipment): Equipment;
    public function update(Equipment $equipment): Equipment;
    public function delete(int $id): void;
    public function countByCompany(int $companyId): int;
    public function countByStatus(int $companyId, string $status): int;
    public function findAvailable(int $companyId): array;
}
