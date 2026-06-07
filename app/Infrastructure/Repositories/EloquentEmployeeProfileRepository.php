<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\EmployeeProfile;
use App\Domain\Repositories\EmployeeProfileRepositoryInterface;
use App\Infrastructure\Models\EmployeeProfileModel;
use DateTimeImmutable;

class EloquentEmployeeProfileRepository implements EmployeeProfileRepositoryInterface
{
    public function findById(int $id): ?EmployeeProfile
    {
        $model = EmployeeProfileModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(int $companyId, array $filters = []): array
    {
        $query = EmployeeProfileModel::where('company_id', $companyId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('nif', 'like', "%{$filters['search']}%")
                  ->orWhere('niss', 'like', "%{$filters['search']}%");
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

    public function save(EmployeeProfile $profile): EmployeeProfile
    {
        $model = new EmployeeProfileModel();
        $model->fill($this->toArray($profile));
        $model->save();

        return $this->toDomain($model);
    }

    public function update(EmployeeProfile $profile): EmployeeProfile
    {
        $model = EmployeeProfileModel::findOrFail($profile->getId());
        $model->fill($this->toArray($profile));
        $model->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        EmployeeProfileModel::findOrFail($id)->delete();
    }

    public function search(string $query, int $companyId): array
    {
        return EmployeeProfileModel::where('company_id', $companyId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('nif', 'like', "%{$query}%")
                  ->orWhere('niss', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    private function toDomain(EmployeeProfileModel $model): EmployeeProfile
    {
        return new EmployeeProfile(
            id: $model->id,
            reference: $model->reference,
            companyId: $model->company_id,
            name: $model->name,
            nif: $model->nif,
            birthDate: $model->birth_date ? new DateTimeImmutable($model->birth_date) : null,
            phone: $model->phone,
            niss: $model->niss,
            documentType: $model->document_type,
            documentNumber: $model->document_number,
            documentIssueDate: $model->document_issue_date ? new DateTimeImmutable($model->document_issue_date) : null,
            documentExpiryDate: $model->document_expiry_date ? new DateTimeImmutable($model->document_expiry_date) : null,
            position: $model->position,
            status: $model->status,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    private function toArray(EmployeeProfile $profile): array
    {
        return [
            'reference' => $profile->getReference(),
            'company_id' => $profile->getCompanyId(),
            'name' => $profile->getName(),
            'nif' => $profile->getNif(),
            'birth_date' => $profile->getBirthDate()?->format('Y-m-d'),
            'phone' => $profile->getPhone(),
            'niss' => $profile->getNiss(),
            'document_type' => $profile->getDocumentType(),
            'document_number' => $profile->getDocumentNumber(),
            'document_issue_date' => $profile->getDocumentIssueDate()?->format('Y-m-d'),
            'document_expiry_date' => $profile->getDocumentExpiryDate()?->format('Y-m-d'),
            'position' => $profile->getPosition(),
            'status' => $profile->getStatus(),
        ];
    }
}
