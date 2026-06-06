<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Department;
use App\Domain\Repositories\DepartmentRepositoryInterface;
use App\Infrastructure\Models\DepartmentModel;

class EloquentDepartmentRepository implements DepartmentRepositoryInterface
{
    public function findById(int $id): ?Department
    {
        $model = DepartmentModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(int $companyId): array
    {
        return DepartmentModel::where('company_id', $companyId)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function save(Department $department): Department
    {
        $model = $this->toModel($department);
        $model->save();
        return $this->toDomain($model);
    }

    public function update(Department $department): Department
    {
        $model = DepartmentModel::findOrFail($department->getId());
        $model->update($this->toArray($department));
        return $this->toDomain($model->fresh());
    }

    public function delete(int $id): void
    {
        DepartmentModel::findOrFail($id)->delete();
    }

    private function toDomain(DepartmentModel $model): Department
    {
        return new Department(
            id: $model->id,
            companyId: $model->company_id,
            name: $model->name,
            description: $model->description,
            managerId: $model->manager_id,
            isActive: $model->is_active,
            createdAt: new \DateTimeImmutable($model->created_at),
            updatedAt: new \DateTimeImmutable($model->updated_at),
        );
    }

    private function toModel(Department $department): DepartmentModel
    {
        $model = $department->getId() ? DepartmentModel::find($department->getId()) ?? new DepartmentModel() : new DepartmentModel();
        $model->fill($this->toArray($department));
        return $model;
    }

    private function toArray(Department $department): array
    {
        return [
            'company_id' => $department->getCompanyId(),
            'name' => $department->getName(),
            'description' => $department->getDescription(),
            'manager_id' => $department->getManagerId(),
            'is_active' => $department->isActive(),
        ];
    }
}
