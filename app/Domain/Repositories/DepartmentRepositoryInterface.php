<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Department;

interface DepartmentRepositoryInterface
{
    public function findById(int $id): ?Department;
    public function findAll(int $companyId): array;
    public function save(Department $department): Department;
    public function update(Department $department): Department;
    public function delete(int $id): void;
}
