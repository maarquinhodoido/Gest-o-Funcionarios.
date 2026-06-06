<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\EmployeeProfile;

interface EmployeeProfileRepositoryInterface
{
    public function findById(int $id): ?EmployeeProfile;
    public function findAll(int $companyId, array $filters = []): array;
    public function save(EmployeeProfile $profile): EmployeeProfile;
    public function update(EmployeeProfile $profile): EmployeeProfile;
    public function delete(int $id): void;
    public function search(string $query, int $companyId): array;
}
