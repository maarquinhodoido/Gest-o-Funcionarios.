<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\EquipmentAssignment;
use App\Domain\Repositories\EquipmentAssignmentRepositoryInterface;
use App\Infrastructure\Models\EquipmentAssignmentModel;

class EloquentEquipmentAssignmentRepository implements EquipmentAssignmentRepositoryInterface
{
    public function findById(int $id): ?EquipmentAssignment
    {
        $model = EquipmentAssignmentModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findActiveByEquipment(int $equipmentId): ?EquipmentAssignment
    {
        $model = EquipmentAssignmentModel::where('equipment_id', $equipmentId)
            ->where('status', EquipmentAssignment::STATUS_ACTIVE)
            ->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function findActiveByUser(int $userId): array
    {
        return EquipmentAssignmentModel::where('user_id', $userId)
            ->where('status', EquipmentAssignment::STATUS_ACTIVE)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findAllByCompany(int $companyId, array $filters = []): array
    {
        $query = EquipmentAssignmentModel::whereHas('equipment', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $models = $query->with(['equipment', 'user'])->orderBy('created_at', 'desc')->paginate($perPage);

        return [
            'items' => array_map(fn($m) => $this->toDomain($m), $models->items()),
            'total' => $models->total(),
            'page' => $models->currentPage(),
            'per_page' => $models->perPage(),
            'last_page' => $models->lastPage(),
        ];
    }

    public function save(EquipmentAssignment $assignment): EquipmentAssignment
    {
        $model = $this->toModel($assignment);
        $model->save();
        return $this->toDomain($model);
    }

    public function update(EquipmentAssignment $assignment): EquipmentAssignment
    {
        $model = EquipmentAssignmentModel::findOrFail($assignment->getId());
        $model->update($this->toArray($assignment));
        return $this->toDomain($model->fresh());
    }

    public function findHistoryByEquipment(int $equipmentId): array
    {
        return EquipmentAssignmentModel::where('equipment_id', $equipmentId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findHistoryByUser(int $userId): array
    {
        return EquipmentAssignmentModel::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function delete(int $id): void
    {
        EquipmentAssignmentModel::findOrFail($id)->delete();
    }

    public function countActiveByCompany(int $companyId): int
    {
        return EquipmentAssignmentModel::whereHas('equipment', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('status', EquipmentAssignment::STATUS_ACTIVE)->count();
    }

    private function toDomain(EquipmentAssignmentModel $model): EquipmentAssignment
    {
        return new EquipmentAssignment(
            id: $model->id,
            reference: $model->reference,
            equipmentId: $model->equipment_id,
            userId: $model->user_id,
            assignedBy: $model->assigned_by,
            assignedAt: $model->assigned_at ? new \DateTimeImmutable($model->assigned_at) : null,
            returnedAt: $model->returned_at ? new \DateTimeImmutable($model->returned_at) : null,
            returnedBy: $model->returned_by,
            status: $model->status,
            notes: $model->notes,
            digitalSignature: $model->digital_signature,
            responsibilityTerm: $model->responsibility_term,
            expectedReturnAt: $model->expected_return_at ? new \DateTimeImmutable($model->expected_return_at) : null,
            createdAt: new \DateTimeImmutable($model->created_at),
            updatedAt: new \DateTimeImmutable($model->updated_at),
        );
    }

    private function toModel(EquipmentAssignment $assignment): EquipmentAssignmentModel
    {
        $model = $assignment->getId() ? EquipmentAssignmentModel::find($assignment->getId()) ?? new EquipmentAssignmentModel() : new EquipmentAssignmentModel();
        $model->fill($this->toArray($assignment));
        return $model;
    }

    private function toArray(EquipmentAssignment $assignment): array
    {
        return [
            'reference' => $assignment->getReference(),
            'equipment_id' => $assignment->getEquipmentId(),
            'user_id' => $assignment->getUserId(),
            'assigned_by' => $assignment->getAssignedBy(),
            'assigned_at' => $assignment->getAssignedAt()?->format('Y-m-d H:i:s'),
            'returned_at' => $assignment->getReturnedAt()?->format('Y-m-d H:i:s'),
            'returned_by' => $assignment->getReturnedBy(),
            'status' => $assignment->getStatus(),
            'notes' => $assignment->getNotes(),
            'digital_signature' => $assignment->getDigitalSignature(),
            'responsibility_term' => $assignment->getResponsibilityTerm(),
            'expected_return_at' => $assignment->getExpectedReturnAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
