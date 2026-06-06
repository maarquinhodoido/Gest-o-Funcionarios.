<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class Notification
{
    private ?int $id;
    private int $companyId;
    private ?int $userId;
    private string $type;
    private string $title;
    private string $message;
    private ?array $data;
    private bool $isRead;
    private ?DateTimeImmutable $readAt;
    private DateTimeImmutable $createdAt;

    public const TYPE_INFO = 'info';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ALERT = 'alert';
    public const TYPE_SUCCESS = 'success';

    public function __construct(
        ?int $id = null,
        int $companyId,
        ?int $userId = null,
        string $type = self::TYPE_INFO,
        string $title,
        string $message,
        ?array $data = null,
        bool $isRead = false,
        ?DateTimeImmutable $readAt = null,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
        $this->isRead = $isRead;
        $this->readAt = $readAt;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getUserId(): ?int { return $this->userId; }
    public function getType(): string { return $this->type; }
    public function getTitle(): string { return $this->title; }
    public function getMessage(): string { return $this->message; }
    public function getData(): ?array { return $this->data; }
    public function isRead(): bool { return $this->isRead; }
    public function getReadAt(): ?DateTimeImmutable { return $this->readAt; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    public function markAsRead(): void
    {
        $this->isRead = true;
        $this->readAt = new DateTimeImmutable();
    }
}
