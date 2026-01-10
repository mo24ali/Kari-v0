<?php

namespace App\Entities\Models;

class Avis
{
    private int $idReservation;
    private int $rating;
    private ?string $comment;
    private ?string $createdAt = null;

    private ?string $authorName = null;

    public function __construct(int $idReservation, int $rating, ?string $comment = null, ?string $createdAt = null)
    {
        $this->idReservation = $idReservation;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
    }


    public function getIdReservation(): int
    {
        return $this->idReservation;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }
    public function setAuthorName(?string $name): void
    {
        $this->authorName = $name;
    }


    public static function fromArray(array $data): self
    {
        $avis = new self(
            (int) $data['id_reservation'],
            (int) $data['rating'],
            $data['comment'] ?? null,
            $data['created_at'] ?? null
        );

        if (isset($data['author_name'])) {
            $avis->setAuthorName($data['author_name']);
        }

        return $avis;
    }
}
