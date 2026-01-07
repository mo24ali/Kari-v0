<?php

namespace App\Entities\Models;

use Exception;

class Logement
{
    private ?int $id = null;
    private int $idOwner;
    private float $price;
    private ?string $address = null;

    public function __construct(int $idOwner, float $price, ?string $address = null, ?int $id = null)
    {
        $this->idOwner = $idOwner;
        $this->price = $price;
        $this->address = $address;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdOwner(): int
    {
        return $this->idOwner;
    }

    public function setIdOwner(int $idOwner): void
    {
        $this->idOwner = $idOwner;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        if ($price <= 0) {
            throw new Exception("Le prix doit être positif.");
        }
        $this->price = $price;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'id_owner' => $this->idOwner,
            'price' => $this->price,
            'address' => $this->address
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_owner'] ?? $data['idOwner'],
            $data['price'],
            $data['address'] ?? null,
            $data['id'] ?? null
        );
    }
}
