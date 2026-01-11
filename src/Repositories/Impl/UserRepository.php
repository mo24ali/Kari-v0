<?php

namespace App\Repositories\Impl;

use App\Repositories\UtilisateurInterface;

use App\core\Database;
use App\Entities\Roles\Utilisateur;
use App\Entities\Roles\Admin;
use App\Entities\Roles\Host;

use PDO;

class UserRepository implements UtilisateurInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ?: null;
    }

    public function save(Utilisateur $user): void
    {
        $sql = "INSERT INTO users(firstname, lastname ,email, password, role, phone) 
                    VALUES (?,?,?,?,?,?)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getPassword(),
            $this->determineRole($user),
            $user->getPhone()
        ]);
    }

    private function determineRole(Utilisateur $user): string
    {
        if ($user instanceof Admin) {
            return 'admin';
        } elseif ($user instanceof Host) {
            return 'host';
        } else {
            return 'traveller';
        }
    }

    public function update(int $userId, array $data): bool|null
    {
        $fields = [];
        $params = [':id' => $userId];

        if (isset($data['firstname'])) {
            $fields[] = 'firstname = :firstname';
            $params[':firstname'] = $data['firstname'];
        }

        if (isset($data['lastname'])) {
            $fields[] = 'lastname = :lastname';
            $params[':lastname'] = $data['lastname'];
        }


        if (isset($data['phone'])) {
            $fields[] = 'phone = :phone';
            $params[':phone'] = $data['phone'];
        }

        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $userId,
            ':password' => $hashedPassword
        ]);
    }


    public function delete(int $id): void
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByRole(string $role, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM users WHERE role = :role ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function countByRole(string $role): int
    {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
}
