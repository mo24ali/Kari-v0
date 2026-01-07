<?php

namespace App\Entities\Models;

class Favoris
{
    private ?int $id;
    private int $user_id;
    private int $logement_id;
    private ?string $added_at;

    public function __construct(
        ?int $id = null,
        int $user_id,
        int $logement_id,
        ?string $added_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->logement_id = $logement_id;
        $this->added_at = $added_at;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getLogementId(): int
    {
        return $this->logement_id;
    }

    public function getAddedAt(): ?string
    {
        return $this->added_at;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setLogementId(int $logement_id): void
    {
        $this->logement_id = $logement_id;
    }

    public function setAddedAt(?string $added_at): void
    {
        $this->added_at = $added_at;
    }
}
