<?php

namespace App\Services;

use App\Repositories\Impl\UserRepository;
use App\Entities\Roles\Host;
use App\Entities\Roles\Admin;
use App\Entities\Roles\Utilisateur;
use App\Entities\Roles\Voyageur;
use Exception;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): ?array
    {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception("L'email et le mot de passe sont requis.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Format d'email invalide.");
            }

            $userData = $this->userRepository->findByEmail($email);

            if (!$userData) {
                throw new Exception("Aucun compte trouvé avec cet email.");
            }

            if (!password_verify($password, $userData['password'])) {
                throw new Exception("Mot de passe incorrect.");
            }

   

            return [
                'id' => $userData['id'],
                'email' => $userData['email'],
                'firstname' => $userData['firstname'] ?? '',
                'lastname' => $userData['lastname'] ?? '',
                'name' => $userData['name'] ?? ($userData['firstname'] . ' ' . $userData['lastname']),
                'role' => $userData['role'] ?? 'traveller',
                'phone' => $userData['phone'] ?? null,
                'created_at' => $userData['created_at']
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function register(array $data): array
    {
        try {
            $this->validateRegistrationData($data);

            if ($this->userRepository->findByEmail($data['email'])) {
                throw new Exception("Cet email est déjà utilisé.");
            }

            $user = $this->createUserFromData($data);

            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));

            $this->userRepository->save($user);

            $savedUser = $this->userRepository->findByEmail($data['email']);

            if (!$savedUser) {
                throw new Exception("Erreur lors de la création du compte.");
            }

            return [
                'firstname' => $savedUser['firstname'],
                'lastname' => $savedUser['lastname'],
                'email' => $savedUser['email'],
                'role' => $savedUser['role'] ?? 'traveller',
                'phone' => $savedUser['phone'] ?? null
            ];
         
        } catch (Exception $e) {
            throw $e;
        }
    }


    //validation method
    private function validateRegistrationData(array $data): void
    {
        $errors = [];

        if (empty($data['firstname']) || strlen($data['firstname']) < 2) {
            $errors[] = "Le prenom doit contenir au moins 2 caractères.";
        }
        if (empty($data['lastname']) || strlen($data['lastname']) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide.";
        }

        if (!empty($data['phone']) && !preg_match('/^[0-9]{10}$/', $data['phone'])) {
            $errors[] = "Numéro de téléphone invalide (10 chiffres requis).";
        }

        if (strlen($data['password']) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        } elseif (!preg_match('/[A-Z]/', $data['password'])) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
        } elseif (!preg_match('/[0-9]/', $data['password'])) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        } elseif ($data['password'] !== ($data['confirm_password'] ?? '')) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        $validRoles = ['traveller', 'host', 'admin'];
        if (!in_array($data['role'], $validRoles)) {
            $errors[] = "Rôle invalide.";
        }

        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }
    }
    //create user from the rendered data
    private function createUserFromData(array $data): Utilisateur
    {

        switch ($data['role']) {
            case 'admin':
                return new Admin(
                    $data['email'],
                    $data['firstname'],
                    $data['lastname'],
                    $data['phone'] ?? null,
                    $data['password']
                );
            case 'host':
                return new Host(
                    $data['email'],
                    $data['firstname'],
                    $data['lastname'],
                    $data['phone'] ?? null,
                    $data['password']
                );
            default:
                return new Voyageur(
                    $data['email'],
                    $data['firstname'],
                    $data['lastname'],
                    $data['phone'] ?? null,
                    $data['password']
                );
        }
    }

    //check if the email exists
    public function emailExists(string $email): bool
    {
        return $this->userRepository->findByEmail($email) !== null;
    }

    public function getUserById(int $id): ?array
    {
        return $this->userRepository->findById($id);
    }

    public function createAdmin(string $fn, string $ln, string $email, string $name, string $password, ?string $phone = null): array
    {
        $data = [
            'firstname' => $fn,
            'lastname' => $ln,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'admin'
        ];

        return $this->register($data);
    }

    public function createHost(string $fn, string $ln, string $email, string $name, string $password, ?string $phone = null): array
    {
        $data = [
            'firstname' => $fn,
            'lastname' => $ln,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'confirm_password' => $password,
            'role' => 'host'
        ];

        return $this->register($data);
    }

    // public function deactivateUser(int $userId): bool
    // {
    //     return $this->userRepository->deactivate($userId);
    // }

    // public function activateUser(int $userId): bool
    // {
    //     return $this->userRepository->activate($userId);
    // }

    public function updateUserProfile(int $userId, array $data): bool
    {
        if (isset($data['firstname']) && strlen($data['firstname']) < 2) {
            throw new Exception("Le prénom doit contenir au moins 2 caractères.");
        }

        if (isset($data['lastname']) && strlen($data['lastname']) < 2) {
            throw new Exception("Le nom doit contenir au moins 2 caractères.");
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide.");
        }

        if (isset($data['email'])) {
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                throw new Exception("Cet email est déjà utilisé par un autre compte.");
            }
        }

        if (isset($data['phone']) && !empty($data['phone']) && !preg_match('/^[0-9]{10}$/', $data['phone'])) {
            throw new Exception("Numéro de téléphone invalide (10 chiffres requis).");
        }

        return $this->userRepository->update($userId, $data);
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $userData = $this->userRepository->findById($userId);

        if (!$userData) {
            throw new Exception("Utilisateur non trouvé.");
        }

        if (!password_verify($currentPassword, $userData['password'])) {
            throw new Exception("Mot de passe actuel incorrect.");
        }

        if (strlen($newPassword) < 8) {
            throw new Exception("Le nouveau mot de passe doit contenir au moins 8 caractères.");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        return $this->userRepository->updatePassword($userId, $hashedPassword);
    }
}
