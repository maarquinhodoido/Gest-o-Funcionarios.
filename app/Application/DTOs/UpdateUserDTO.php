<?php

namespace App\Application\DTOs;

class UpdateUserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?int $employeeProfileId = null,
        public readonly ?int $departmentId = null,
        public readonly ?int $positionId = null,
        public readonly ?string $hireDate = null,
        public readonly ?string $status = null,
        public readonly array $roles = [],
    ) {}
}
