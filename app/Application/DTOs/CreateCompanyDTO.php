<?php

namespace App\Application\DTOs;

class CreateCompanyDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $legalName,
        public readonly string $taxId,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $country = null,
        public readonly ?string $postalCode = null,
        public readonly string $plan = 'free',
        public readonly int $maxUsers = 10,
    ) {}
}
