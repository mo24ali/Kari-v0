<?php

namespace App\services;


interface AuthService
{

    public function isAuth(): bool;
    public function getUserRole(): string | null;
    public function login(array $user): void; 
    public function logout(): void;
}
