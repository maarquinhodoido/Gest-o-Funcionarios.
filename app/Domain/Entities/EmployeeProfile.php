<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use JsonSerializable;

class EmployeeProfile implements JsonSerializable
{
    private ?int $id;
    private ?string $reference;
    private int $companyId;
    private string $name;
    private ?string $nif;
    private ?DateTimeImmutable $birthDate;
    private ?string $phone;
    private ?string $niss;
    private ?string $documentType;
    private ?string $documentNumber;
    private ?DateTimeImmutable $documentIssueDate;
    private ?DateTimeImmutable $documentExpiryDate;
    private ?string $position;
    private string $status;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $companyId,
        string $name,
        ?int $id = null,
        ?string $reference = null,
        ?string $nif = null,
        ?DateTimeImmutable $birthDate = null,
        ?string $phone = null,
        ?string $niss = null,
        ?string $documentType = null,
        ?string $documentNumber = null,
        ?DateTimeImmutable $documentIssueDate = null,
        ?DateTimeImmutable $documentExpiryDate = null,
        ?string $position = null,
        string $status = 'active',
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->companyId = $companyId;
        $this->name = $name;
        $this->nif = $nif;
        $this->birthDate = $birthDate;
        $this->phone = $phone;
        $this->niss = $niss;
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->documentIssueDate = $documentIssueDate;
        $this->documentExpiryDate = $documentExpiryDate;
        $this->position = $position;
        $this->status = $status;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getReference(): ?string { return $this->reference; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getName(): string { return $this->name; }
    public function getNif(): ?string { return $this->nif; }
    public function getBirthDate(): ?DateTimeImmutable { return $this->birthDate; }
    public function getPhone(): ?string { return $this->phone; }
    public function getNiss(): ?string { return $this->niss; }
    public function getDocumentType(): ?string { return $this->documentType; }
    public function getDocumentNumber(): ?string { return $this->documentNumber; }
    public function getDocumentIssueDate(): ?DateTimeImmutable { return $this->documentIssueDate; }
    public function getDocumentExpiryDate(): ?DateTimeImmutable { return $this->documentExpiryDate; }
    public function getPosition(): ?string { return $this->position; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isActive(): bool { return $this->status === 'active'; }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'company_id' => $this->companyId,
            'name' => $this->name,
            'nif' => $this->nif,
            'birth_date' => $this->birthDate?->format('Y-m-d'),
            'phone' => $this->phone,
            'niss' => $this->niss,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
            'document_issue_date' => $this->documentIssueDate?->format('Y-m-d'),
            'document_expiry_date' => $this->documentExpiryDate?->format('Y-m-d'),
            'position' => $this->position,
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
