<?php

namespace App\Application\DTOs;

class AssignEquipmentDTO
{
    public function __construct(
        public readonly int $equipmentId,
        public readonly int $userId,
        public readonly int $assignedBy,
        public readonly ?string $reference = null,
        public readonly ?string $notes = null,
        public readonly ?string $expectedReturnAt = null,
        public readonly ?string $responsibilityTerm = null,
    ) {}
}
