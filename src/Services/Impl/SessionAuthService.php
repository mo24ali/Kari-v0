<?php

namespace App\Services\Impl;

use App\Services\AuthService;

use function App\dump_die;

class SessionAuthService implements AuthService
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isAuth(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function getUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUser(): ?array
    {
        // dump_die($_SESSION['user']);
        return $_SESSION['user'] ?? null;
    }

    public function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['user_role'] = $user['role'] ?? 'traveller';
        $_SESSION['user_email'] = $user['email'] ?? '';
        $_SESSION['user_phone'] = $user['phone'] ?? '';

        if (isset($user['firstname']) && isset($user['lastname'])) {
            $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        } elseif (isset($user['name'])) {
            $_SESSION['user_name'] = $user['name'];
        } else {
            $_SESSION['user_name'] = 'Utilisateur';
        }

        $_SESSION['user_firstname'] = $user['firstname'] ?? '';
        $_SESSION['user_lastname'] = $user['lastname'] ?? '';

        $_SESSION['user'] = $user;

        $_SESSION['user_permissions'] = $this->getPermissionsForRole($_SESSION['user_role']);

        $_SESSION['user_permissions'] = $this->getPermissionsForRole($user['role']);

        // session_regenerate_id(true);
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    public function requireAuth(): void
    {
        if (!$this->isAuth()) {
            header("Location: /login");
            exit;
        }
    }

    public function requireRole(string $role): void
    {
        $this->requireAuth();

        if ($this->getUserRole() !== $role) {
            header("Location: /");
            exit;
        }
    }

    public function hasRole(string $role): bool
    {
        return $this->getUserRole() === $role;
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->isAuth()) {
            return false;
        }

        $permissions = $_SESSION['user_permissions'] ?? [];
        return in_array($permission, $permissions);
    }

    private function getPermissionsForRole(string $role): array
    {
        switch ($role) {
            case 'admin':
                return [
                    'manage_all_users',
                    'create_user',
                    'edit_user',
                    'delete_user',
                    'suspend_user',
                    'manage_all_content',
                    'approve_content',
                    'delete_any_content',
                    'access_admin_panel',
                    'manage_system_settings',
                    'view_analytics',
                    'manage_payments',
                    'manage_reports',
                    'approve_listings',
                    'feature_listings',
                    'manage_categories'
                ];
            case 'host':
                return [
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
            case 'traveller':
            default:
                return [
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
    }
}
