<?php

namespace App\Entities\Roles;



class Host extends Utilisateur
{
    private array $properties = [];
    private float $earnings = 0.0;
    private float $rating = 0.0;
    private bool $isVerified = false;

    public function __construct(
        string $email,
        string $firstname,
        string $lastname,
        string $phone,
        string $passwd
    ) {
        parent::__construct(
            $firstname,
            $lastname,
            $email,
            $phone,
            $passwd
        );
        $this->role = 'host';
    }

    protected function setDefaultPermissions(): void
    {
        $this->permissions = [
            'create_property',
            'edit_property',
            'delete_property',
            'manage_property_listings',
            'update_property_availability',

            'view_bookings',
            'manage_bookings',
            'approve_booking',
            'cancel_booking',

            'view_earnings',
            'withdraw_earnings',
            'view_transactions',

            'respond_to_reviews',
            'view_guest_reviews',

            'update_host_profile',
            'upload_documents'
        ];
    }

    public function addProperty(int $propertyId): void
    {
        $this->properties[] = $propertyId;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addEarnings(float $amount): void
    {
        $this->earnings += $amount;
    }

    public function getEarnings(): float
    {
        return $this->earnings;
    }

    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function verify(): void
    {
        $this->isVerified = true;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }
}
