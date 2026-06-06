<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Phone;
use DateTimeImmutable;

class User
{
    private ?int $id;
    private string $name;
    private Email $email;
    private ?Phone $phone;
    private string $password;
    private int $companyId;
    private ?int $employeeProfileId;
    private ?int $departmentId;
    private ?int $positionId;
    private ?DateTimeImmutable $hireDate;
    private string $status;
    private bool $isOnline;
    private ?string $profilePhoto;
    private ?string $twoFactorSecret;
    private bool $twoFactorEnabled;
    private ?DateTimeImmutable $emailVerifiedAt;
    private ?DateTimeImmutable $lastLoginAt;
    private ?string $lastLoginIp;
    private ?string $lastLoginUserAgent;
    private ?DateTimeImmutable $passwordChangedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        ?int $id = null,
        string $name,
        Email $email,
        string $password,
        int $companyId,
        ?Phone $phone = null,
        ?int $employeeProfileId = null,
        ?int $departmentId = null,
        ?int $positionId = null,
        ?DateTimeImmutable $hireDate = null,
        string $status = 'active',
        bool $isOnline = false,
        ?string $profilePhoto = null,
        ?string $twoFactorSecret = null,
        bool $twoFactorEnabled = false,
        ?DateTimeImmutable $emailVerifiedAt = null,
        ?DateTimeImmutable $lastLoginAt = null,
        ?string $lastLoginIp = null,
        ?string $lastLoginUserAgent = null,
        ?DateTimeImmutable $passwordChangedAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->companyId = $companyId;
        $this->phone = $phone;
        $this->employeeProfileId = $employeeProfileId;
        $this->departmentId = $departmentId;
        $this->positionId = $positionId;
        $this->hireDate = $hireDate;
        $this->status = $status;
        $this->isOnline = $isOnline;
        $this->profilePhoto = $profilePhoto;
        $this->twoFactorSecret = $twoFactorSecret;
        $this->twoFactorEnabled = $twoFactorEnabled;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->lastLoginAt = $lastLoginAt;
        $this->lastLoginIp = $lastLoginIp;
        $this->lastLoginUserAgent = $lastLoginUserAgent;
        $this->passwordChangedAt = $passwordChangedAt;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): Email { return $this->email; }
    public function getPhone(): ?Phone { return $this->phone; }
    public function getPassword(): string { return $this->password; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getEmployeeProfileId(): ?int { return $this->employeeProfileId; }
    public function getDepartmentId(): ?int { return $this->departmentId; }
    public function getPositionId(): ?int { return $this->positionId; }
    public function getHireDate(): ?DateTimeImmutable { return $this->hireDate; }
    public function getStatus(): string { return $this->status; }
    public function isOnline(): bool { return $this->isOnline; }
    public function getProfilePhoto(): ?string { return $this->profilePhoto; }
    public function getTwoFactorSecret(): ?string { return $this->twoFactorSecret; }
    public function isTwoFactorEnabled(): bool { return $this->twoFactorEnabled; }
    public function getEmailVerifiedAt(): ?DateTimeImmutable { return $this->emailVerifiedAt; }
    public function getLastLoginAt(): ?DateTimeImmutable { return $this->lastLoginAt; }
    public function getLastLoginIp(): ?string { return $this->lastLoginIp; }
    public function getLastLoginUserAgent(): ?string { return $this->lastLoginUserAgent; }
    public function getPasswordChangedAt(): ?DateTimeImmutable { return $this->passwordChangedAt; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isBlocked(): bool { return $this->status === 'blocked'; }
    public function isInactive(): bool { return $this->status === 'inactive'; }

    public function markAsOnline(): void { $this->isOnline = true; }
    public function markAsOffline(): void { $this->isOnline = false; }
    public function block(): void { $this->status = 'blocked'; }
    public function activate(): void { $this->status = 'active'; }
    public function deactivate(): void { $this->status = 'inactive'; }

    public function updateLastLogin(string $ip, string $userAgent): void
    {
        $this->lastLoginAt = new DateTimeImmutable();
        $this->lastLoginIp = $ip;
        $this->lastLoginUserAgent = $userAgent;
    }

    public function enableTwoFactor(string $secret): void
    {
        $this->twoFactorSecret = $secret;
        $this->twoFactorEnabled = true;
    }

    public function disableTwoFactor(): void
    {
        $this->twoFactorSecret = null;
        $this->twoFactorEnabled = false;
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = $newPassword;
        $this->passwordChangedAt = new DateTimeImmutable();
    }

    public function verifyEmail(): void
    {
        $this->emailVerifiedAt = new DateTimeImmutable();
    }
}
