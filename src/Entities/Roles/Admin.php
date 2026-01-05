<?php
// app/Entities/Roles/Admin.php
namespace App\Entities\Roles;


class Admin extends Utilisateur
{
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
        $this->role = 'admin';
    }

    protected function setDefaultPermissions(): void
    {
        $this->permissions = [
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
    }
}
