<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateUserDTO;
use App\Application\DTOs\UpdateUserDTO;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Phone;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CompanyRepositoryInterface $companyRepository,
        private AuditService $auditService,
        private NotificationService $notificationService,
    ) {}

    public function create(CreateUserDTO $dto): User
    {
        $existing = $this->userRepository->findByEmail($dto->email);
        if ($existing) {
            throw new \DomainException('User with this email already exists.');
        }

        $company = $this->companyRepository->findById($dto->companyId);
        if (!$company) {
            throw new \DomainException('Company not found.');
        }

        $user = new User(
            name: $dto->name,
            email: new Email($dto->email),
            password: Hash::make($dto->password),
            companyId: $dto->companyId,
            phone: $dto->phone ? new Phone($dto->phone) : null,
            employeeProfileId: $dto->employeeProfileId,
            departmentId: $dto->departmentId,
            positionId: $dto->positionId,
            hireDate: $dto->hireDate ? new \DateTimeImmutable($dto->hireDate) : null,
        );

        $saved = $this->userRepository->save($user);

        $this->auditService->log(
            companyId: $dto->companyId,
            userId: $saved->getId(),
            action: AuditLog::ACTION_CREATE,
            entityType: 'user',
            entityId: $saved->getId(),
            description: "User created: {$saved->getName()} ({$saved->getEmail()})",
        );

        return $saved;
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        return $this->userRepository->findAll($companyId, $filters);
    }

    public function update(UpdateUserDTO $dto): User
    {
        $user = $this->userRepository->findById($dto->id);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $changes = [];

        if ($dto->name !== null) {
            $changes['name'] = ['old' => $user->getName(), 'new' => $dto->name];
        }

        $saved = $this->userRepository->update($user);

        if (!empty($changes)) {
            $this->auditService->log(
                companyId: $saved->getCompanyId(),
                userId: $saved->getId(),
                action: AuditLog::ACTION_UPDATE,
                entityType: 'user',
                entityId: $saved->getId(),
                oldValues: $changes,
                description: "User updated: {$saved->getName()}",
            );
        }

        return $saved;
    }

    public function block(int $id): User
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $user->block();
        $saved = $this->userRepository->update($user);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            userId: $saved->getId(),
            action: AuditLog::ACTION_BLOCK,
            entityType: 'user',
            entityId: $id,
            severity: AuditLog::SEVERITY_WARNING,
            description: "User blocked: {$saved->getName()}",
        );

        return $saved;
    }

    public function unblock(int $id): User
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $user->activate();
        $saved = $this->userRepository->update($user);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            userId: $saved->getId(),
            action: AuditLog::ACTION_UNBLOCK,
            entityType: 'user',
            entityId: $id,
            description: "User unblocked: {$saved->getName()}",
        );

        return $saved;
    }

    public function deactivate(int $id): User
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $user->deactivate();
        $saved = $this->userRepository->update($user);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            userId: $saved->getId(),
            action: AuditLog::ACTION_UPDATE,
            entityType: 'user',
            entityId: $id,
            description: "User deactivated: {$saved->getName()}",
        );

        return $saved;
    }

    public function resetPassword(int $id, string $newPassword): User
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $user->changePassword(Hash::make($newPassword));
        $saved = $this->userRepository->update($user);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            userId: $saved->getId(),
            action: AuditLog::ACTION_PASSWORD_CHANGE,
            entityType: 'user',
            entityId: $id,
            severity: AuditLog::SEVERITY_WARNING,
            description: "Password reset for user: {$saved->getName()}",
        );

        return $saved;
    }

    public function delete(int $id): void
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $this->auditService->log(
            companyId: $user->getCompanyId(),
            userId: $id,
            action: AuditLog::ACTION_DELETE,
            entityType: 'user',
            entityId: $id,
            severity: AuditLog::SEVERITY_WARNING,
            description: "User deleted: {$user->getName()}",
        );

        $this->userRepository->delete($id);
    }

    public function search(string $query, int $companyId): array
    {
        return $this->userRepository->search($query, $companyId);
    }
}
