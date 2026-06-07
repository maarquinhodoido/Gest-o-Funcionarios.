<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Company;
use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Domain\ValueObjects\Email;
use App\Infrastructure\Models\CompanyModel;

class EloquentCompanyRepository implements CompanyRepositoryInterface
{
    public function findById(int $id): ?Company
    {
        $model = CompanyModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByTaxId(string $taxId): ?Company
    {
        $model = CompanyModel::where('tax_id', $taxId)->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(array $filters = []): array
    {
        $query = CompanyModel::query();
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['plan'])) {
            $query->where('plan', $filters['plan']);
        }
        return $query->orderBy('created_at', 'desc')->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function save(Company $company): Company
    {
        $model = $this->toModel($company);
        $model->save();
        return $this->toDomain($model);
    }

    public function update(Company $company): Company
    {
        $model = CompanyModel::findOrFail($company->getId());
        $model->update($this->toArray($company));
        return $this->toDomain($model->fresh());
    }

    public function delete(int $id): void
    {
        CompanyModel::findOrFail($id)->delete();
    }

    public function count(): int
    {
        return CompanyModel::count();
    }

    private function toDomain(CompanyModel $model): Company
    {
        return new Company(
            id: $model->id,
            reference: $model->reference,
            name: $model->name,
            legalName: $model->legal_name,
            taxId: $model->tax_id,
            email: $model->email ? new Email($model->email) : null,
            phone: $model->phone,
            address: $model->address,
            city: $model->city,
            state: $model->state,
            country: $model->country,
            postalCode: $model->postal_code,
            plan: $model->plan,
            status: $model->status,
            maxUsers: $model->max_users,
            logo: $model->logo,
            primaryColor: $model->primary_color,
            trialEndsAt: $model->trial_ends_at ? new \DateTimeImmutable($model->trial_ends_at) : null,
            subscribedAt: $model->subscribed_at ? new \DateTimeImmutable($model->subscribed_at) : null,
            subscriptionEndsAt: $model->subscription_ends_at ? new \DateTimeImmutable($model->subscription_ends_at) : null,
            createdAt: new \DateTimeImmutable($model->created_at),
            updatedAt: new \DateTimeImmutable($model->updated_at),
        );
    }

    private function toModel(Company $company): CompanyModel
    {
        $model = $company->getId() ? CompanyModel::find($company->getId()) ?? new CompanyModel() : new CompanyModel();
        $model->fill($this->toArray($company));
        return $model;
    }

    private function toArray(Company $company): array
    {
        return [
            'reference' => $company->getReference(),
            'name' => $company->getName(),
            'legal_name' => $company->getLegalName(),
            'tax_id' => $company->getTaxId(),
            'email' => $company->getEmail()?->value(),
            'phone' => $company->getPhone(),
            'address' => $company->getAddress(),
            'city' => $company->getCity(),
            'state' => $company->getState(),
            'country' => $company->getCountry(),
            'postal_code' => $company->getPostalCode(),
            'plan' => $company->getPlan(),
            'status' => $company->getStatus(),
            'max_users' => $company->getMaxUsers(),
            'logo' => $company->getLogo(),
            'primary_color' => $company->getPrimaryColor(),
            'trial_ends_at' => $company->getTrialEndsAt()?->format('Y-m-d H:i:s'),
            'subscribed_at' => $company->getSubscribedAt()?->format('Y-m-d H:i:s'),
            'subscription_ends_at' => $company->getSubscriptionEndsAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
