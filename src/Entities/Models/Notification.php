<?php

namespace App\Entities\Models;

class Notification
{
    private int $idUser;
    private string $type;
    private string $message;
    private bool $isRead;
    private ?string $createdAt = null;

    public function __construct(int $idUser, string $type, string $message, bool $isRead = false, ?string $createdAt = null)
    {
        $this->idUser = $idUser;
        $this->type = $type;
        $this->message = $message;
        $this->isRead = $isRead;
        $this->createdAt = $createdAt;
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function isRead(): bool
    {
        return $this->isRead;
    }
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id_user'],
            $data['type'],
            $data['message'],
            (bool) ($data['is_read'] ?? 0),
            $data['created_at'] ?? null
        );
    }
}
