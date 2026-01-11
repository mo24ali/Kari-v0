<?php

namespace App\Entities\Models;

use Exception;

class Logement
{
    private ?int $id = null;
    private int $idOwner;
    private float $price;
    private ?string $address = null;
    private array $images = []; //let it be array of links
    private ?string $primaryImage = null;
    private ?array $owner = null;//let it be bunch of names stored into an array

    public function __construct(int $idOwner, float $price, ?string $address = null, ?int $id = null)
    {
        $this->idOwner = $idOwner;
        $this->price = $price;
        $this->address = $address;
        $this->id = $id;
    }

    // getters / setters
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

    public function getPrice(): float
    {
        return $this->price;
    }
    public function setPrice(float $price): void
    {
        if ($price <= 0)
            throw new Exception("Le prix doit être positif.");
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

    public function getImages(): array
    {
        return $this->images;
    }
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getPrimaryImage(): ?string
    {
        return $this->primaryImage;
    }
    public function setPrimaryImage(?string $image): void
    {
        $this->primaryImage = $image;
    }

    public function getOwner(): ?array
    {
        return $this->owner;
    }
    public function setOwner(?array $owner): void
    {
        $this->owner = $owner;
    }
    //parse object data to array
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'id_owner' => $this->idOwner,
            'price' => $this->price,
            'address' => $this->address,
            'images' => $this->images,
            'primary_image' => $this->primaryImage,
            'owner' => $this->owner
        ];
    }
    //create new objects out of an array
    public static function fromArray(array $data): self
    {
        $logement = new self(
            (int) ($data['id_owner'] ?? $data['idOwner']),
            (float) $data['price'],
            $data['address'] ?? null,
            isset($data['id']) ? (int) $data['id'] : null
        );

        if (isset($data['owner_email'])) {
            $logement->setOwner([
                'firstname' => $data['firstname'] ?? '',
                'lastname' => $data['lastname'] ?? '',
                'email' => $data['owner_email'] ?? ''
            ]);
        }

        if (isset($data['primary_image'])) {
            $logement->setPrimaryImage($data['primary_image']);
        }

        if (isset($data['images'])) {
            $logement->setImages($data['images']);
        }

        return $logement;
    }
}
