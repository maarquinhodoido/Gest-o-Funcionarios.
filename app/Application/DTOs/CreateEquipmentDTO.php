<?php

namespace App\Application\DTOs;

class CreateEquipmentDTO
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $equipmentTypeId,
        public readonly string $serialNumber,
        public readonly string $brand,
        public readonly string $model,
        public readonly ?string $location = null,
        public readonly ?string $warrantyEnd = null,
        public readonly ?string $purchaseDate = null,
        public readonly ?float $purchasePrice = null,
        public readonly ?string $supplier = null,
        public readonly ?string $notes = null,
    ) {}
}
