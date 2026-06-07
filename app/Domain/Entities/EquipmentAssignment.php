<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use JsonSerializable;

class EquipmentAssignment implements JsonSerializable
{
    private ?int $id;
    private ?string $reference;
    private int $equipmentId;
    private int $userId;
    private int $assignedBy;
    private ?DateTimeImmutable $assignedAt;
    private ?DateTimeImmutable $returnedAt;
    private ?int $returnedBy;
    private string $status;
    private ?string $notes;
    private ?string $digitalSignature;
    private ?string $responsibilityTerm;
    private ?DateTimeImmutable $expectedReturnAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_LOST = 'lost';

    public function __construct(
        ?int $id = null,
        ?string $reference = null,
        int $equipmentId,
        int $userId,
        int $assignedBy,
        ?DateTimeImmutable $assignedAt = null,
        ?DateTimeImmutable $returnedAt = null,
        ?int $returnedBy = null,
        string $status = self::STATUS_ACTIVE,
        ?string $notes = null,
        ?string $digitalSignature = null,
        ?string $responsibilityTerm = null,
        ?DateTimeImmutable $expectedReturnAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->equipmentId = $equipmentId;
        $this->userId = $userId;
        $this->assignedBy = $assignedBy;
        $this->assignedAt = $assignedAt ?? new DateTimeImmutable();
        $this->returnedAt = $returnedAt;
        $this->returnedBy = $returnedBy;
        $this->status = $status;
        $this->notes = $notes;
        $this->digitalSignature = $digitalSignature;
        $this->responsibilityTerm = $responsibilityTerm;
        $this->expectedReturnAt = $expectedReturnAt;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getReference(): ?string { return $this->reference; }
    public function getEquipmentId(): int { return $this->equipmentId; }
    public function getUserId(): int { return $this->userId; }
    public function getAssignedBy(): int { return $this->assignedBy; }
    public function getAssignedAt(): ?DateTimeImmutable { return $this->assignedAt; }
    public function getReturnedAt(): ?DateTimeImmutable { return $this->returnedAt; }
    public function getReturnedBy(): ?int { return $this->returnedBy; }
    public function getStatus(): string { return $this->status; }
    public function getNotes(): ?string { return $this->notes; }
    public function getDigitalSignature(): ?string { return $this->digitalSignature; }
    public function getResponsibilityTerm(): ?string { return $this->responsibilityTerm; }
    public function getExpectedReturnAt(): ?DateTimeImmutable { return $this->expectedReturnAt; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }

    public function return(int $returnedBy): void
    {
        $this->status = self::STATUS_RETURNED;
        $this->returnedAt = new DateTimeImmutable();
        $this->returnedBy = $returnedBy;
    }

    public function markLost(): void
    {
        $this->status = self::STATUS_LOST;
    }

    public function addSignature(string $signature): void
    {
        $this->digitalSignature = $signature;
    }

    public function addResponsibilityTerm(string $term): void
    {
        $this->responsibilityTerm = $term;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'equipment_id' => $this->equipmentId,
            'user_id' => $this->userId,
            'assigned_by' => $this->assignedBy,
            'assigned_at' => $this->assignedAt?->format('Y-m-d H:i:s'),
            'returned_at' => $this->returnedAt?->format('Y-m-d H:i:s'),
            'returned_by' => $this->returnedBy,
            'status' => $this->status,
            'notes' => $this->notes,
            'digital_signature' => $this->digitalSignature,
            'responsibility_term' => $this->responsibilityTerm,
            'expected_return_at' => $this->expectedReturnAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
