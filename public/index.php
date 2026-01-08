<?php

namespace App;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'src/config/connexion.php';
use Exception;
use App\core\Database;
use App\KariApp;
use App\Services\Impl\SessionAuthService;
use App\Services\UserService;
use App\Services\LogementService;
use App\Services\BookingService;
use App\Services\UploadService;
use App\Repositories\Impl\UserRepository;
use App\Repositories\Impl\LogementRepository;
use App\Repositories\Impl\ReservationRepository;
use App\Repositories\Impl\ImageRepository;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = Database::getInstance()->getConnection();

$userRepository = new UserRepository();
$userService = new UserService($userRepository);
$authService = new SessionAuthService();
$logementRepository = new LogementRepository();
$logementService = new LogementService($logementRepository);
$reservationRepository = new ReservationRepository();
$bookingService = new BookingService($reservationRepository, $logementRepository);
$imageRepository = new ImageRepository();
$uploadService = new UploadService();

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/login') {
    try {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        $user = $userService->login($email, $password);
        
        if ($user) {
            $authService->login($user);
            $_SESSION['success'] = "Connexion réussie !";
            header("Location: /");
            exit;
        } else {
            $_SESSION['error'] = "Identifiants incorrects.";
            header("Location: /login");
            exit;
        }
    } catch (Exception $e) {
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
                header("Location: /views/hote.php");
                break;
            default:
                header("Location: /");
        }
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header("Location: /signup");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/logement/add') {
    if (!$authService->isAuth() || $authService->getUserRole() !== 'host') {
        $_SESSION['error'] = "Vous devez être connecté en tant qu'hôte pour ajouter un logement.";
        header("Location: /hote");
        exit;
    }
    
    try {
        $data = [
            'address' => trim($_POST['address'] ?? ''),
            'price' => $_POST['price'] ?? 0
        ];
        
        $logement = $logementService->createLogement($data, $authService->getUserId());
        $logementId = $logement->getId();
        
        if (isset($_FILES['images']) && !empty($_FILES['images']['tmp_name'][0])) {
            $uploadedPaths = $uploadService->uploadMultipleImages($_FILES['images'], $logementId);
            
            foreach ($uploadedPaths as $index => $imagePath) {
                $imageRepository->save([
                    'id_logement' => $logementId,
                    'image_path' => $imagePath,
                    'is_primary' => $index === 0 ? 1 : 0 
                ]);
            }
        }
        
        $_SESSION['success'] = "Logement ajouté avec succès !";
        header("Location: /hote");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header("Location: /hote");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/logement/update') {
    if (!$authService->isAuth() || $authService->getUserRole() !== 'host') {
        $_SESSION['error'] = "Vous devez être connecté en tant qu'hôte pour modifier un logement.";
        header("Location: /hote");
        exit;
    }
    
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'address' => trim($_POST['address'] ?? ''),
            'price' => $_POST['price'] ?? 0
        ];
        
        $logementService->updateLogement($id, $data, $authService->getUserId());
        
        if (isset($_FILES['images']) && !empty($_FILES['images']['tmp_name'][0])) {
            $uploadedPaths = $uploadService->uploadMultipleImages($_FILES['images'], $id);
            
            foreach ($uploadedPaths as $index => $imagePath) {
                $imageRepository->save([
                    'id_logement' => $id,
                    'image_path' => $imagePath,
                    'is_primary' => 0 
                ]);
            }
        }
        
        $_SESSION['success'] = "Logement modifié avec succès !";
        header("Location: /hote");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header("Location: /hote");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/logement/delete') {
    if (!$authService->isAuth() || $authService->getUserRole() !== 'host') {
        $_SESSION['error'] = "Vous devez être connecté en tant qu'hôte pour supprimer un logement.";
        header("Location: /hote");
        exit;
    }
    
    try {
        $id = (int) ($_POST['id'] ?? 0);
        
        $images = $imageRepository->findByLogement($id);
        foreach ($images as $image) {
            $uploadService->deleteImage($image['image_path']);
        }
        $imageRepository->deleteByLogement($id);
        
        $logementService->deleteLogement($id, $authService->getUserId());
        $_SESSION['success'] = "Logement supprimé avec succès !";
        header("Location: /hote");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header("Location: /hote");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/reservation/create') {
    if (!$authService->isAuth()) {
        $_SESSION['error'] = "Vous devez être connecté pour effectuer une réservation.";
        header("Location: /login");
        exit;
    }
    
    try {
        $data = [
            'id_log' => (int) ($_POST['id_log'] ?? 0),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? ''
        ];
        
        $bookingService->createReservation($data, $authService->getUserId());
        $_SESSION['success'] = "Réservation créée avec succès !";
        header("Location: /reservations");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header("Location: /");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/profile/update') {
    if (!$authService->isAuth()) {
        $_SESSION['error'] = "Vous devez être connecté pour modifier votre profil.";
        header("Location: /login");
        exit;
    }
    
    try {
        $data = [
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'phone' => trim($_POST['phone'] ?? '')
        ];
        
        $userService->updateUserProfile($authService->getUserId(), $data);
        
        $updatedUser = $userRepository->findById($authService->getUserId());
        if ($updatedUser) {
            $_SESSION['user_firstname'] = $updatedUser['firstname'];
            $_SESSION['user_lastname'] = $updatedUser['lastname'];
            $_SESSION['user_name'] = $updatedUser['firstname'] . ' ' . $updatedUser['lastname'];
            $_SESSION['user_email'] = $updatedUser['email'];
            $_SESSION['user_phone'] = $updatedUser['phone'] ?? '';
            $_SESSION['user'] = [
                'id' => $updatedUser['id'],
                'email' => $updatedUser['email'],
                'firstname' => $updatedUser['firstname'],
                'lastname' => $updatedUser['lastname'],
                'phone' => $updatedUser['phone'] ?? '',
                'role' => $updatedUser['role'] ?? 'traveller'
            ];
        }
        
        $_SESSION['success'] = "Profil mis à jour avec succès !";
        header("Location: /profile");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header("Location: /profile");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/profile/change-password') {
    if (!$authService->isAuth()) {
        $_SESSION['error'] = "Vous devez être connecté pour changer votre mot de passe.";
        header("Location: /login");
        exit;
    }
    
    try {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($newPassword !== $confirmPassword) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }
        
        $userService->changePassword($authService->getUserId(), $currentPassword, $newPassword);
        $_SESSION['success'] = "Mot de passe modifié avec succès !";
        header("Location: /profile");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header("Location: /profile");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/logement/filter') {
    if(!$authService->isAuth()){
        $_SESSION['error'] = "vous devez etre connecté en tant qu'utilisateur pour effectuer cette opp";
        header("Location: /");
        exit();
    }

    try{
        $adress;

    }catch(Exception $e){
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/logement/details') {
    if(!$authService->isAuth()){
        $_SESSION['error'] = "vous devez etre connecté en tant qu'utilisateur pour effectuer cette opp";
        header("Location: /");
        exit();
    }

    try{
      
        

    }catch(Exception $e){
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
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
function dump_die($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

$app = KariApp::init($authService);
$app->render();