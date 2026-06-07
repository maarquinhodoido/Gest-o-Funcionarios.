<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use JsonSerializable;

class Department implements JsonSerializable
{
    private ?int $id;
    private ?string $reference;
    private int $companyId;
    private string $name;
    private ?string $description;
    private ?int $managerId;
    private bool $isActive;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $companyId,
        string $name,
        ?int $id = null,
        ?string $reference = null,
        ?string $description = null,
        ?int $managerId = null,
        bool $isActive = true,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->companyId = $companyId;
        $this->name = $name;
        $this->description = $description;
        $this->managerId = $managerId;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getReference(): ?string { return $this->reference; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getManagerId(): ?int { return $this->managerId; }
    public function isActive(): bool { return $this->isActive; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function activate(): void { $this->isActive = true; }
    public function deactivate(): void { $this->isActive = false; }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'company_id' => $this->companyId,
            'name' => $this->name,
            'description' => $this->description,
            'manager_id' => $this->managerId,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
