<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Equipment;
use App\Domain\Repositories\EquipmentRepositoryInterface;
use App\Infrastructure\Models\EquipmentModel;

class EloquentEquipmentRepository implements EquipmentRepositoryInterface
{
    public function findById(int $id): ?Equipment
    {
        $model = EquipmentModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findBySerialNumber(string $serial, int $companyId): ?Equipment
    {
        $model = EquipmentModel::where('serial_number', $serial)
            ->where('company_id', $companyId)
            ->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        $query = EquipmentModel::where('company_id', $companyId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['equipment_type_id'])) {
            $query->where('equipment_type_id', $filters['equipment_type_id']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('serial_number', 'like', "%{$filters['search']}%")
                  ->orWhere('brand', 'like', "%{$filters['search']}%")
                  ->orWhere('model', 'like', "%{$filters['search']}%");
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

    public function save(Equipment $equipment): Equipment
    {
        $model = $this->toModel($equipment);
        $model->save();
        return $this->toDomain($model);
    }

    public function update(Equipment $equipment): Equipment
    {
        $model = EquipmentModel::findOrFail($equipment->getId());
        $model->update($this->toArray($equipment));
        return $this->toDomain($model->fresh());
    }

    public function delete(int $id): void
    {
        EquipmentModel::findOrFail($id)->delete();
    }

    public function countByCompany(int $companyId): int
    {
        return EquipmentModel::where('company_id', $companyId)->count();
    }

    public function countByStatus(int $companyId, string $status): int
    {
        return EquipmentModel::where('company_id', $companyId)
            ->where('status', $status)
            ->count();
    }

    public function findAvailable(int $companyId): array
    {
        return EquipmentModel::where('company_id', $companyId)
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    private function toDomain(EquipmentModel $model): Equipment
    {
        return new Equipment(
            id: $model->id,
            companyId: $model->company_id,
            equipmentTypeId: $model->equipment_type_id,
            serialNumber: $model->serial_number,
            brand: $model->brand,
            model: $model->model,
            status: $model->status,
            location: $model->location,
            warrantyEnd: $model->warranty_end ? new \DateTimeImmutable($model->warranty_end) : null,
            purchaseDate: $model->purchase_date ? new \DateTimeImmutable($model->purchase_date) : null,
            purchasePrice: $model->purchase_price,
            supplier: $model->supplier,
            notes: $model->notes,
            qrCode: $model->qr_code,
            createdAt: new \DateTimeImmutable($model->created_at),
            updatedAt: new \DateTimeImmutable($model->updated_at),
        );
    }

    private function toModel(Equipment $equipment): EquipmentModel
    {
        $model = $equipment->getId() ? EquipmentModel::find($equipment->getId()) ?? new EquipmentModel() : new EquipmentModel();
        $model->fill($this->toArray($equipment));
        return $model;
    }

    private function toArray(Equipment $equipment): array
    {
        return [
            'company_id' => $equipment->getCompanyId(),
            'equipment_type_id' => $equipment->getEquipmentTypeId(),
            'serial_number' => $equipment->getSerialNumber(),
            'brand' => $equipment->getBrand(),
            'model' => $equipment->getModel(),
            'status' => $equipment->getStatus(),
            'location' => $equipment->getLocation(),
            'warranty_end' => $equipment->getWarrantyEnd()?->format('Y-m-d'),
            'purchase_date' => $equipment->getPurchaseDate()?->format('Y-m-d'),
            'purchase_price' => $equipment->getPurchasePrice(),
            'supplier' => $equipment->getSupplier(),
            'notes' => $equipment->getNotes(),
            'qr_code' => $equipment->getQrCode(),
        ];
    }
}
