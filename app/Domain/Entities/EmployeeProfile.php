<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class EmployeeProfile
{
    private ?int $id;
    private int $companyId;
    private string $name;
    private ?string $nif;
    private ?DateTimeImmutable $birthDate;
    private ?string $phone;
    private ?string $niss;
    private ?string $position;
    private string $status;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        ?int $id = null,
        int $companyId,
        string $name,
        ?string $nif = null,
        ?DateTimeImmutable $birthDate = null,
        ?string $phone = null,
        ?string $niss = null,
        ?string $position = null,
        string $status = 'active',
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->name = $name;
        $this->nif = $nif;
        $this->birthDate = $birthDate;
        $this->phone = $phone;
        $this->niss = $niss;
        $this->position = $position;
        $this->status = $status;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getName(): string { return $this->name; }
    public function getNif(): ?string { return $this->nif; }
    public function getBirthDate(): ?DateTimeImmutable { return $this->birthDate; }
    public function getPhone(): ?string { return $this->phone; }
    public function getNiss(): ?string { return $this->niss; }
    public function getPosition(): ?string { return $this->position; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isActive(): bool { return $this->status === 'active'; }
}
