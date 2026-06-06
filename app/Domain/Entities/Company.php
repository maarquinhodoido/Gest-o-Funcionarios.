<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use DateTimeImmutable;

class Company
{
    private ?int $id;
    private string $name;
    private string $legalName;
    private string $taxId;
    private ?Email $email;
    private ?string $phone;
    private ?string $address;
    private ?string $city;
    private ?string $state;
    private ?string $country;
    private ?string $postalCode;
    private string $plan;
    private string $status;
    private int $maxUsers;
    private ?string $logo;
    private ?string $primaryColor;
    private ?DateTimeImmutable $trialEndsAt;
    private ?DateTimeImmutable $subscribedAt;
    private ?DateTimeImmutable $subscriptionEndsAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        ?int $id = null,
        string $name,
        string $legalName,
        string $taxId,
        ?Email $email = null,
        ?string $phone = null,
        ?string $address = null,
        ?string $city = null,
        ?string $state = null,
        ?string $country = null,
        ?string $postalCode = null,
        string $plan = 'free',
        string $status = 'active',
        int $maxUsers = 10,
        ?string $logo = null,
        ?string $primaryColor = null,
        ?DateTimeImmutable $trialEndsAt = null,
        ?DateTimeImmutable $subscribedAt = null,
        ?DateTimeImmutable $subscriptionEndsAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->legalName = $legalName;
        $this->taxId = $taxId;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->plan = $plan;
        $this->status = $status;
        $this->maxUsers = $maxUsers;
        $this->logo = $logo;
        $this->primaryColor = $primaryColor;
        $this->trialEndsAt = $trialEndsAt;
        $this->subscribedAt = $subscribedAt;
        $this->subscriptionEndsAt = $subscriptionEndsAt;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getLegalName(): string { return $this->legalName; }
    public function getTaxId(): string { return $this->taxId; }
    public function getEmail(): ?Email { return $this->email; }
    public function getPhone(): ?string { return $this->phone; }
    public function getAddress(): ?string { return $this->address; }
    public function getCity(): ?string { return $this->city; }
    public function getState(): ?string { return $this->state; }
    public function getCountry(): ?string { return $this->country; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function getPlan(): string { return $this->plan; }
    public function getStatus(): string { return $this->status; }
    public function getMaxUsers(): int { return $this->maxUsers; }
    public function getLogo(): ?string { return $this->logo; }
    public function getPrimaryColor(): ?string { return $this->primaryColor; }
    public function getTrialEndsAt(): ?DateTimeImmutable { return $this->trialEndsAt; }
    public function getSubscribedAt(): ?DateTimeImmutable { return $this->subscribedAt; }
    public function getSubscriptionEndsAt(): ?DateTimeImmutable { return $this->subscriptionEndsAt; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isOnTrial(): bool { return $this->trialEndsAt && $this->trialEndsAt > new DateTimeImmutable(); }

    public function activate(): void { $this->status = 'active'; }
    public function suspend(): void { $this->status = 'suspended'; }
    public function cancel(): void { $this->status = 'cancelled'; }

    public function changePlan(string $plan): void { $this->plan = $plan; }
    public function setMaxUsers(int $max): void { $this->maxUsers = $max; }
}
