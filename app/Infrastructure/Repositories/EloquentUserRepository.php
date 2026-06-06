<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Phone;
use App\Infrastructure\Models\UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        $query = UserModel::where('company_id', $companyId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        $perPage = $filters['per_page'] ?? 15;
        $models = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return [
            'items' => array_map(fn($m) => $this->toDomain($m), $models->items()),
            'total' => $models->total(),
            'page' => $models->currentPage(),
            'per_page' => $models->perPage(),
            'last_page' => $models->lastPage(),
        ];
    }

    public function save(User $user): User
    {
        $model = $this->toModel($user);
        $model->save();

        if (!$user->getId()) {
            $model->syncRoles([]);
        }

        return $this->toDomain($model);
    }

    public function update(User $user): User
    {
        $model = UserModel::findOrFail($user->getId());
        $model->fill($this->toArray($user));
        $model->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        UserModel::findOrFail($id)->delete();
    }

    public function countByCompany(int $companyId): int
    {
        return UserModel::where('company_id', $companyId)->count();
    }

    public function findActiveByCompany(int $companyId): array
    {
        return UserModel::where('company_id', $companyId)
            ->where('status', 'active')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findByDepartment(int $departmentId): array
    {
        return UserModel::where('department_id', $departmentId)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function search(string $query, int $companyId): array
    {
        return UserModel::where('company_id', $companyId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    private function toDomain(UserModel $model): User
    {
        return new User(
            id: $model->id,
            name: $model->name,
            email: new Email($model->email),
            password: $model->password,
            companyId: $model->company_id,
            phone: $model->phone ? new Phone($model->phone) : null,
            employeeProfileId: $model->employee_profile_id,
            departmentId: $model->department_id,
            positionId: $model->position_id,
            hireDate: $model->hire_date ? new \DateTimeImmutable($model->hire_date) : null,
            status: $model->status,
            isOnline: $model->is_online,
            profilePhoto: $model->profile_photo,
            twoFactorSecret: $model->two_factor_secret,
            twoFactorEnabled: $model->two_factor_enabled,
            emailVerifiedAt: $model->email_verified_at ? new \DateTimeImmutable($model->email_verified_at) : null,
            lastLoginAt: $model->last_login_at ? new \DateTimeImmutable($model->last_login_at) : null,
            lastLoginIp: $model->last_login_ip,
            lastLoginUserAgent: $model->last_login_user_agent,
            passwordChangedAt: $model->password_changed_at ? new \DateTimeImmutable($model->password_changed_at) : null,
            createdAt: new \DateTimeImmutable($model->created_at),
            updatedAt: new \DateTimeImmutable($model->updated_at),
        );
    }

    private function toModel(User $user): UserModel
    {
        $model = $user->getId() ? UserModel::find($user->getId()) ?? new UserModel() : new UserModel();
        $model->fill($this->toArray($user));
        return $model;
    }

    private function toArray(User $user): array
    {
        return [
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
            'password' => $user->getPassword(),
            'company_id' => $user->getCompanyId(),
            'employee_profile_id' => $user->getEmployeeProfileId(),
            'phone' => $user->getPhone()?->value(),
            'department_id' => $user->getDepartmentId(),
            'position_id' => $user->getPositionId(),
            'hire_date' => $user->getHireDate()?->format('Y-m-d'),
            'status' => $user->getStatus(),
            'is_online' => $user->isOnline(),
            'profile_photo' => $user->getProfilePhoto(),
            'two_factor_secret' => $user->getTwoFactorSecret(),
            'two_factor_enabled' => $user->isTwoFactorEnabled(),
            'email_verified_at' => $user->getEmailVerifiedAt()?->format('Y-m-d H:i:s'),
            'last_login_at' => $user->getLastLoginAt()?->format('Y-m-d H:i:s'),
            'last_login_ip' => $user->getLastLoginIp(),
            'last_login_user_agent' => $user->getLastLoginUserAgent(),
            'password_changed_at' => $user->getPasswordChangedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
