<?php

namespace App;

use App\Services\AuthService;

class KariApp
{
    private static ?KariApp $instance = null;
    private AuthService $auth;

    private function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public static function init(AuthService $auth): self
    {
        if (self::$instance === null) {
            self::$instance = new self($auth);
        }
        return self::$instance;
    }

    public function render()
    {
        if ($this->auth->isAuth()) {
            $this->run();
        } else {
            require __DIR__ . '/views/hero.php';
        }
    }

    private function run()
    {
        $role = $this->auth->getUserRole();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        switch ($path) {
            case '/':
            case '/index.php':
                require __DIR__ . '/views/index.php';
                break;
            case '/reservations':
                require __DIR__ . '/views/reservations.php';
                break;
            case '/favoris':
                require __DIR__ . '/views/favoris.php';
                break;
            case '/hote':
                require __DIR__ . '/views/hote.php';
                break;
            case '/profile':
                require __DIR__ . '/views/profile.php';
                break;
            case '/admin':
                if ($role === 'admin') {
                    require __DIR__ . '/views/DashboardAdmin.php';
                } else {
                    echo "Access Denied: Admins Only";
                }
                break;
            default:
                http_response_code(404);
                echo "Page not found.";
                break;
        }
    }
}
