<?php


namespace App\Repositories;

use App\Entities\Roles\Utilisateur;

interface UtilisateurInterface
{

    public function findByEmail(string $email): ?array;
    public function save(Utilisateur $user): void;
    public function findById(int $id): ?array;
    public function update(int $userId, array $data): bool|null;
    public function updatePassword(int $userId, string $hashedPassword): bool;
    public function delete(int $id): void;
    public function findAll(int $limit = 50, int $offset = 0): array;
    public function findByRole(string $role, int $limit = 50, int $offset = 0): array;
    public function count(): int;
    public function countByRole(string $role): int;
}
