<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Company;

interface CompanyRepositoryInterface
{
    public function findById(int $id): ?Company;
    public function findByTaxId(string $taxId): ?Company;
    public function findAll(array $filters = []): array;
    public function save(Company $company): Company;
    public function update(Company $company): Company;
    public function delete(int $id): void;
    public function count(): int;
}
