<?php

namespace App;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'src/config/connexion.php';

use App\core\Database;
use App\KariApp;
use App\services\impl\SessionAuthService;
use App\services\UserService;
use App\repositories\Impl\UserRepository;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = Database::getInstance()->getConnection();

$userRepository = new UserRepository();
$userService = new UserService($userRepository);
$authService = new SessionAuthService();

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/login') {
    try {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        $user = $userService->login($email, $password);
        
        if ($user) {
            $authService->login($user);
            $_SESSION['success'] = "Connexion réussie !";
            
            switch ($user['role']) {
                case 'admin':
                    header("Location: /admin");
                    break;
                case 'host':
                    header("Location: /host/dashboard");
                    break;
                default:
                    header("Location: /");
            }
            exit;
        } else {
            $_SESSION['error'] = "Identifiants incorrects.";
            header("Location: /login");
            exit;
        }
    } catch (\Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header("Location: /login");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/signup') {
    try {
        $data = [
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'role' => $_POST['role'] ?? 'traveller' 
        ];
        
        $user = $userService->register($data);
        $authService->login($user);
        
        $_SESSION['success'] = "Inscription réussie ! Bienvenue sur KARI.";
        
        switch ($user['role']) {
            case 'host':
                header("Location: /host/welcome");
                break;
            default:
                header("Location: /");
        }
        exit;
        
    } catch (\Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header("Location: /signup");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($requestPath) {
        case '/login':
            require_once BASE_PATH . 'src/views/login.php';
            exit;
        case '/signup':
            require_once BASE_PATH . 'src/views/signup.php';
            exit;
        case '/logout':
            $authService->logout();
            header("Location: /");
            exit;
    }
}

$app = KariApp::init($authService);
$app->render();