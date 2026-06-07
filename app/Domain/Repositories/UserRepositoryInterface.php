<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findAll(int $companyId, array $filters = []): array;
    public function save(User $user, array $roles = []): User;
    public function update(User $user, array $roles = []): User;
    public function delete(int $id): void;
    public function countByCompany(int $companyId): int;
    public function findActiveByCompany(int $companyId): array;
    public function findByDepartment(int $departmentId): array;
    public function search(string $query, int $companyId): array;
}
