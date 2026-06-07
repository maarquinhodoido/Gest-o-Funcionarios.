<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use JsonSerializable;

class Equipment implements JsonSerializable
{
    private ?int $id;
    private ?string $reference;
    private int $companyId;
    private int $equipmentTypeId;
    private string $serialNumber;
    private string $brand;
    private string $model;
    private string $status;
    private ?string $location;
    private ?DateTimeImmutable $warrantyEnd;
    private ?DateTimeImmutable $purchaseDate;
    private ?float $purchasePrice;
    private ?string $supplier;
    private ?string $notes;
    private ?string $qrCode;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_LOST = 'lost';
    public const STATUS_DISABLED = 'disabled';

    public function __construct(
        ?int $id = null,
        ?string $reference = null,
        int $companyId,
        int $equipmentTypeId,
        string $serialNumber,
        string $brand,
        string $model,
        string $status = self::STATUS_AVAILABLE,
        ?string $location = null,
        ?DateTimeImmutable $warrantyEnd = null,
        ?DateTimeImmutable $purchaseDate = null,
        ?float $purchasePrice = null,
        ?string $supplier = null,
        ?string $notes = null,
        ?string $qrCode = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->companyId = $companyId;
        $this->equipmentTypeId = $equipmentTypeId;
        $this->serialNumber = $serialNumber;
        $this->brand = $brand;
        $this->model = $model;
        $this->status = $status;
        $this->location = $location;
        $this->warrantyEnd = $warrantyEnd;
        $this->purchaseDate = $purchaseDate;
        $this->purchasePrice = $purchasePrice;
        $this->supplier = $supplier;
        $this->notes = $notes;
        $this->qrCode = $qrCode;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getReference(): ?string { return $this->reference; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getEquipmentTypeId(): int { return $this->equipmentTypeId; }
    public function getSerialNumber(): string { return $this->serialNumber; }
    public function getBrand(): string { return $this->brand; }
    public function getModel(): string { return $this->model; }
    public function getStatus(): string { return $this->status; }
    public function getLocation(): ?string { return $this->location; }
    public function getWarrantyEnd(): ?DateTimeImmutable { return $this->warrantyEnd; }
    public function getPurchaseDate(): ?DateTimeImmutable { return $this->purchaseDate; }
    public function getPurchasePrice(): ?float { return $this->purchasePrice; }
    public function getSupplier(): ?string { return $this->supplier; }
    public function getNotes(): ?string { return $this->notes; }
    public function getQrCode(): ?string { return $this->qrCode; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isAvailable(): bool { return $this->status === self::STATUS_AVAILABLE; }
    public function isAssigned(): bool { return $this->status === self::STATUS_ASSIGNED; }

    public function assign(): void { $this->status = self::STATUS_ASSIGNED; }
    public function markAvailable(): void { $this->status = self::STATUS_AVAILABLE; }
    public function markMaintenance(): void { $this->status = self::STATUS_MAINTENANCE; }
    public function markLost(): void { $this->status = self::STATUS_LOST; }
    public function disable(): void { $this->status = self::STATUS_DISABLED; }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'company_id' => $this->companyId,
            'equipment_type_id' => $this->equipmentTypeId,
            'serial_number' => $this->serialNumber,
            'brand' => $this->brand,
            'model' => $this->model,
            'status' => $this->status,
            'location' => $this->location,
            'warranty_end' => $this->warrantyEnd?->format('Y-m-d'),
            'purchase_date' => $this->purchaseDate?->format('Y-m-d'),
            'purchase_price' => $this->purchasePrice,
            'supplier' => $this->supplier,
            'notes' => $this->notes,
            'qr_code' => $this->qrCode,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
