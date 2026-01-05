<?php


namespace App\Repositories;

use App\Entities\Roles\Utilisateur;

interface UserInterface {
    
    public function findByEmail(string $email): ?array;
    public function save(Utilisateur $user): void;
}
