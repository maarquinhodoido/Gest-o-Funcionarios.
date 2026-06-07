<?php

namespace App\Application\DTOs;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly int $companyId,
        public readonly ?string $reference = null,
        public readonly ?string $phone = null,
        public readonly ?int $employeeProfileId = null,
        public readonly ?int $departmentId = null,
        public readonly ?int $positionId = null,
        public readonly ?string $hireDate = null,
        public readonly array $roles = [],
    ) {}
}
