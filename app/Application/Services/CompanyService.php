<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateCompanyDTO;
use App\Domain\Entities\Company;
use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Domain\ValueObjects\Email;

class CompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private AuditService $auditService,
    ) {}

    public function create(CreateCompanyDTO $dto): Company
    {
        $existing = $this->companyRepository->findByTaxId($dto->taxId);
        if ($existing) {
            throw new \DomainException('Company with this Tax ID already exists.');
        }

        $company = new Company(
            name: $dto->name,
            legalName: $dto->legalName,
            taxId: $dto->taxId,
            email: $dto->email ? new Email($dto->email) : null,
            phone: $dto->phone,
            address: $dto->address,
            city: $dto->city,
            state: $dto->state,
            country: $dto->country,
            postalCode: $dto->postalCode,
            plan: $dto->plan,
            maxUsers: $dto->maxUsers,
        );

        $saved = $this->companyRepository->save($company);

        $this->auditService->log(
            companyId: $saved->getId(),
            action: AuditLog::ACTION_CREATE,
            entityType: 'company',
            entityId: $saved->getId(),
            description: "Company created: {$saved->getName()}",
        );

        return $saved;
    }

    public function findById(int $id): ?Company
    {
        return $this->companyRepository->findById($id);
    }

    public function findAll(array $filters = []): array
    {
        return $this->companyRepository->findAll($filters);
    }

    public function update(int $id, array $data): Company
    {
        $company = $this->companyRepository->findById($id);
        if (!$company) {
            throw new \DomainException('Company not found.');
        }

        $reflection = new \ReflectionClass($company);
        foreach ($data as $field => $value) {
            $method = 'set' . ucfirst($field);
            if ($reflection->hasMethod($method)) {
                $company->$method($value);
            }
        }

        $updated = $this->companyRepository->update($company);

        $this->auditService->log(
            companyId: $id,
            action: AuditLog::ACTION_UPDATE,
            entityType: 'company',
            entityId: $id,
            description: "Company updated: {$updated->getName()}",
        );

        return $updated;
    }

    public function delete(int $id): void
    {
        $company = $this->companyRepository->findById($id);
        if (!$company) {
            throw new \DomainException('Company not found.');
        }

        $this->companyRepository->delete($id);

        $this->auditService->log(
            companyId: $id,
            action: AuditLog::ACTION_DELETE,
            entityType: 'company',
            entityId: $id,
            description: "Company deleted: {$company->getName()}",
        );
    }

    public function canAddUsers(int $companyId): bool
    {
        $company = $this->companyRepository->findById($companyId);
        if (!$company) {
            return false;
        }

        $currentUsers = $this->companyRepository->count();
        return $currentUsers < $company->getMaxUsers();
    }
}
