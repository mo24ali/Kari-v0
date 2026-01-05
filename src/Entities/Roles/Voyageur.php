<?php

namespace App\Entities\Roles;



class Voyageur extends Utilisateur
{
    private array $bookings = [];
    private array $wishlist = [];


    public function __construct(
        string $email,
        string $firstname,
        string $lastname,
        string $phone,
        string $passwd,
        bool $isActive = true
    ) {
        parent::__construct(
            $firstname,
            $lastname,
            $email,
            $phone,
            $passwd,
            $isActive
        );
        $this->role = 'traveller';
    }

    protected function setDefaultPermissions(): void
    {
        $this->permissions = [
            'search_properties',
            'book_property',
            'view_bookings',
            'cancel_booking',
            'write_review',
            'edit_review',
            'delete_review',
            'update_profile',
            'manage_payment_methods',
            'view_booking_history',
            'message_host',
            'view_messages',
            'add_to_wishlist',
            'manage_wishlist'
        ];
    }



    public function addBooking(int $bookingId): void
    {
        $this->bookings[] = $bookingId;
    }

    public function getBookings(): array
    {
        return $this->bookings;
    }

    public function addToWishlist(int $propertyId): void
    {
        $this->wishlist[] = $propertyId;
    }

    public function getWishlist(): array
    {
        return $this->wishlist;
    }

    //     public function setPreferredLanguage(string $language): void
    //     {
    //         $this->preferredLanguage = $language;
    //     }

    //     public function getPreferredLanguage(): string
    //     {
    //         return $this->preferredLanguage;
    //     }

    //     public function setCurrency(string $currency): void
    //     {
    //         $this->currency = $currency;
    //     }

    //     public function getCurrency(): string
    //     {
    //         return $this->currency;
    //     }
}
