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
            case '/reservation/cancel':
                $controller = new \App\Controllers\ReservationController();
                $controller->cancel();
                break;
            case '/favoris/add':
                $controller = new \App\Controllers\FavorisController();
                $controller->add();
                break;
            case '/favoris/remove':
                $controller = new \App\Controllers\FavorisController();
                $controller->remove();
                break;
            case '/admin/users':
                if ($role === 'admin') {
                    require __DIR__ . '/views/admin/users.php';
                } else {
                    echo "Access Denied: Admins Only";
                }
                break;
            case '/admin/logements':
                if ($role === 'admin') {
                    require __DIR__ . '/views/admin/logements.php';
                } else {
                    echo "Access Denied: Admins Only";
                }
                break;
            case '/admin/reclamations':
                if ($role === 'admin') {
                    require __DIR__ . '/views/admin/reclamations.php';
                } else {
                    echo "Access Denied: Admins Only";
                }
                break;
            case '/reclamation/create':
                if (!$this->auth->isAuth()) {
                    header('Location: /login');
                    exit;
                }
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $userId = $_SESSION['user_id'];
                    $logementId = $_POST['logement_id'] ?? null;
                    $message = $_POST['message'] ?? null;

                    if ($logementId && $message) {
                        $repo = new \App\Repositories\ReclamationRepository();
                        $service = new \App\Services\ReclamationService($repo);
                        $service->createReclamation($userId, (int) $logementId, $message);
                        $_SESSION['success'] = "Réclamation envoyée avec succès.";
                    }
                    header('Location: /reservations');
                    exit;
                }
                break;
            default:
                http_response_code(404);
                echo "Page not found.";
                break;
        }
    }
}
