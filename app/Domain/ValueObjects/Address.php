<?php

namespace App\Domain\ValueObjects;

class Address
{
    private string $street;
    private string $city;
    private string $state;
    private string $country;
    private string $postalCode;

    public function __construct(
        string $street,
        string $city,
        string $state,
        string $country,
        string $postalCode
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->postalCode = $postalCode;
    }

    public function street(): string { return $this->street; }
    public function city(): string { return $this->city; }
    public function state(): string { return $this->state; }
    public function country(): string { return $this->country; }
    public function postalCode(): string { return $this->postalCode; }

    public function full(): string
    {
        return "{$this->street}, {$this->city}, {$this->state}, {$this->postalCode}, {$this->country}";
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
        ];
    }
}
