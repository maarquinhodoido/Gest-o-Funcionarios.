<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use JsonSerializable;

class AuditLog implements JsonSerializable
{
    private ?int $id;
    private int $companyId;
    private ?int $userId;
    private string $action;
    private string $entityType;
    private ?int $entityId;
    private ?array $oldValues;
    private ?array $newValues;
    private string $ipAddress;
    private ?string $userAgent;
    private ?string $location;
    private string $severity;
    private ?string $description;
    private ?string $userName;
    private DateTimeImmutable $createdAt;

    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_LOGIN_FAILED = 'login_failed';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_ASSIGN = 'assign';
    public const ACTION_RETURN = 'return';
    public const ACTION_BLOCK = 'block';
    public const ACTION_UNBLOCK = 'unblock';
    public const ACTION_PERMISSION_CHANGE = 'permission_change';
    public const ACTION_PASSWORD_CHANGE = 'password_change';
    public const ACTION_2FA_ENABLE = '2fa_enable';
    public const ACTION_2FA_DISABLE = '2fa_disable';

    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_CRITICAL = 'critical';

    public function __construct(
        ?int $id = null,
        int $companyId,
        ?int $userId = null,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $ipAddress = '',
        ?string $userAgent = null,
        ?string $location = null,
        string $severity = self::SEVERITY_INFO,
        ?string $description = null,
        ?string $userName = null,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->action = $action;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->location = $location;
        $this->severity = $severity;
        $this->description = $description;
        $this->userName = $userName;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getUserId(): ?int { return $this->userId; }
    public function getAction(): string { return $this->action; }
    public function getEntityType(): string { return $this->entityType; }
    public function getEntityId(): ?int { return $this->entityId; }
    public function getOldValues(): ?array { return $this->oldValues; }
    public function getNewValues(): ?array { return $this->newValues; }
    public function getIpAddress(): string { return $this->ipAddress; }
    public function getUserAgent(): ?string { return $this->userAgent; }
    public function getLocation(): ?string { return $this->location; }
    public function getSeverity(): string { return $this->severity; }
    public function getDescription(): ?string { return $this->description; }
    public function getUserName(): ?string { return $this->userName; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    public function isCritical(): bool { return $this->severity === self::SEVERITY_CRITICAL; }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->companyId,
            'user_id' => $this->userId,
            'action' => $this->action,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'old_values' => $this->oldValues,
            'new_values' => $this->newValues,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'location' => $this->location,
            'severity' => $this->severity,
            'description' => $this->description,
            'user_name' => $this->userName,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
