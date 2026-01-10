<?php

namespace App;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'src/config/connexion.php';

use Exception;
use App\Core\Database;
use App\Core\Router;
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
use App\Services\FavorisService;
use App\Repositories\Impl\FavorisRepository;
use App\Repositories\Impl\ReclamationRepository;
use App\Repositories\Impl\NotificationRepository;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authService = new SessionAuthService();
$userRepository = new UserRepository();
$userService = new UserService($userRepository);
$logementRepository = new LogementRepository();
$logementService = new LogementService($logementRepository);
$reservationRepository = new ReservationRepository();
$bookingService = new BookingService($reservationRepository, $logementRepository);
$imageRepository = new ImageRepository();
$uploadService = new UploadService();
$favorisService = new FavorisService(new FavorisRepository());

$router = new Router();


$requireAuth = function () use ($authService) {
    if (!$authService->isAuth()) {
        $_SESSION['error'] = "Vous devez être connecté.";
        header("Location: /login");
        exit;
    }
    return $authService->getUserId();
};

// routes

$router->get('/', function () use ($authService) {
    require BASE_PATH . 'src/views/index.php';
});

$router->get('/login', function () use ($authService) {
    require BASE_PATH . 'src/views/login.php';
});

$router->get('/signup', function () use ($authService) {
    require BASE_PATH . 'src/views/signup.php';
});

$router->get('/logout', function () use ($authService) {
    $authService->logout();
    header("Location: /");
    exit;
});

$router->post('/login', function () use ($userService, $authService) {
    try {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $user = $userService->login($email, $password); 
        if ($user) {
            $authService->login($user);
            $_SESSION['success'] = "Connexion réussie !";
            header("Location: /");
        } else {
            $_SESSION['error'] = "Identifiants incorrects.";
            header("Location: /login");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header("Location: /login");
    }
    exit;
});

$router->post('/signup', function () use ($userService, $authService) {
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
        $_SESSION['success'] = "Inscription réussie !";
        header("Location: /");
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header("Location: /signup");
    }
    exit;
});

// functionalities  

$router->post('/logement/add', function () use ($authService, $logementService, $uploadService, $imageRepository) {
    $userId = $authService->getUserId(); // basic auth check without redirect if needed, but we use middleware usually
    if (!$authService->isAuth() || $authService->getUserRole() !== 'host') {
        header("Location: /hote");
        exit;
    }

    try {
        $data = [
            'address' => trim($_POST['address'] ?? ''),
            'price' => $_POST['price'] ?? 0
        ];
        $logement = $logementService->createLogement($data, $userId);

        if (isset($_FILES['images']) && !empty($_FILES['images']['tmp_name'][0])) {
            $uploadedPaths = $uploadService->uploadMultipleImages($_FILES['images'], $logement->getId());
            foreach ($uploadedPaths as $index => $imagePath) {
                $imageRepository->save([
                    'id_logement' => $logement->getId(),
                    'image_path' => $imagePath,
                    'is_primary' => $index === 0 ? 1 : 0
                ]);
            }
        }
        $_SESSION['success'] = "Logement ajouté !";
        header("Location: /hote");
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /hote");
    }
    exit;
});

$router->post('/logement/update', function () use ($authService, $logementService, $uploadService, $imageRepository) {
    if (!$authService->isAuth() || $authService->getUserRole() !== 'host') {
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
            foreach ($uploadedPaths as $imagePath) {
                $imageRepository->save([
                    'id_logement' => $id,
                    'image_path' => $imagePath,
                    'is_primary' => 0 // Newer images are not primary by default for now
                ]);
            }
        }

        $_SESSION['success'] = "Logement mis à jour !";
        header("Location: /hote");
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /hote");
    }
    exit;
});

$router->post('/logement/delete', function () use ($authService, $logementService) {
    if (!$authService->isAuth() || $authService->getUserRole() !== 'host') {
        header("Location: /hote");
        exit;
    }

    try {
        $id = (int) ($_POST['id'] ?? 0);
        $logementService->deleteLogement($id, $authService->getUserId());
        $_SESSION['success'] = "Logement supprimé !";
        header("Location: /hote");
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /hote");
    }
    exit;
});

$router->post('/reservation/create', function () use ($requireAuth, $bookingService) {
    $userId = $requireAuth();
    try {
        $data = [
            'id_log' => (int) ($_POST['id_log'] ?? 0),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? ''
        ];
        $bookingService->createReservation($data, $userId);
        $_SESSION['success'] = "Réservation créée !";
        header("Location: /reservations");
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /");
    }
    exit;
});

$router->post('/reservation/cancel', function () use ($requireAuth, $bookingService) {
    $userId = $requireAuth();
    $reservationId = $_POST['reservation_id'] ?? null;
    if ($reservationId) {
        $bookingService->cancelReservation((int) $reservationId, $userId);
    }
    header("Location: /reservations");
    exit;
});

$router->post('/favoris/add', function () use ($requireAuth, $favorisService) {
    $userId = $requireAuth();
    $logementId = $_POST['logement_id'] ?? null;
    if ($logementId) {
        $favorisService->addToFavoris($userId, (int) $logementId);
    }
    $referer = $_SERVER['HTTP_REFERER'] ?? '/favoris';
    header("Location: $referer");
    exit;
});

$router->post('/favoris/remove', function () use ($requireAuth, $favorisService) {
    $userId = $requireAuth();
    $logementId = $_POST['logement_id'] ?? null;
    if ($logementId) {
        $favorisService->removeFromFavoris($userId, (int) $logementId);
    }
    $referer = $_SERVER['HTTP_REFERER'] ?? '/favoris';
    header("Location: $referer");
    exit;
});

$router->post('/reclamation/create', function () use ($requireAuth) {
    $userId = $requireAuth();
    $logementId = (int) ($_POST['logement_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($logementId <= 0 || empty($message)) {
        $_SESSION['error'] = "Données invalides pour la réclamation.";
        header("Location: /reservations");
        exit;
    }

    $reclamationRepo = new \App\Repositories\Impl\ReclamationRepository();
    $logementRepo = new \App\Repositories\Impl\LogementRepository();

    // Create reclamation
    $reclamationRepo->create($userId, $logementId, $message);

    // Notify Host
    $logement = $logementRepo->findById($logementId);
    if ($logement) {
        $notifRepo = new \App\Repositories\Impl\NotificationRepository();
        $notifService = new \App\Services\NotificationService($notifRepo);

        $hostId = $logement->getIdOwner();
        $notifService->notifyUser(
            $hostId,
            'reclamation',
            "Un voyageur a signalé un problème pour votre logement situé à : " . $logement->getAddress()
        );
    }

    $_SESSION['success'] = "Votre réclamation a été envoyée. L'hôte et l'administration ont été notifiés.";
    header("Location: /reservations");
    exit;
});

$router->post('/notifications/mark-as-read', function () use ($requireAuth) {
    $userId = $requireAuth();
    $notifRepo = new \App\Repositories\Impl\NotificationRepository();
    $notifRepo->markAllReadByUser($userId);
    http_response_code(200);
    exit;
});

// View Routes
$router->get('/reservations', function () use ($requireAuth, $authService) {
    $requireAuth();
    require BASE_PATH . 'src/views/reservations.php';
});

$router->get('/favoris', function () use ($requireAuth, $authService) {
    $requireAuth();
    require BASE_PATH . 'src/views/favoris.php';
});

$router->get('/hote', function () use ($authService) {
    if (!$authService->isAuth()) {
        header("Location: /login");
        exit;
    }
    require BASE_PATH . 'src/views/hote.php';
});

$router->get('/profile', function () use ($requireAuth, $authService) {
    $requireAuth();
    require BASE_PATH . 'src/views/profile.php';
});

$router->get('/admin', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        echo "Access Denied";
        exit;
    }
    require BASE_PATH . 'src/views/DashboardAdmin.php';
});

$router->get('/receipt', function () use ($requireAuth, $reservationRepository, $logementRepository, $userRepository) {
    $userId = $requireAuth();
    $id = (int) ($_GET['id'] ?? 0);

    $reservation = $reservationRepository->findById($id);

    if (!$reservation) {
        $_SESSION['error'] = "Réservation introuvable.";
        header("Location: /reservations");
        exit;
    }

    // Authorization Check
    // Can view if it's your reservation OR if you are the host of the logement
    $logement = $logementRepository->findById($reservation['id_log']);

    if (!$logement) {
        $_SESSION['error'] = "Logement associé introuvable.";
        header("Location: /reservations");
        exit;
    }

    if ($reservation['id_user'] != $userId && $logement->getIdOwner() != $userId && $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = "Accès non autorisé.";
        header("Location: /reservations");
        exit;
    }

    $user = $userRepository->findById($reservation['id_user']);

    require BASE_PATH . 'src/views/receipt.php';
});

$router->get('/logement/details', function () use ($logementService, $authService) {
    $avisRepository = new \App\Repositories\Impl\AvisRepository();
    $avisService = new \App\Services\AvisService($avisRepository);

    $id = (int) ($_GET['id'] ?? 0);
    $logement = $logementService->getLogementById($id);

    if (!$logement) {
        header("Location: /");
        exit;
    }

    $reviews = $avisService->getLogementReviews($id);
    $averageRating = $avisService->getAverageRating($id);

    require BASE_PATH . 'src/views/logDetails.php';
});

$router->post('/review/add', function () use ($requireAuth) {
    $userId = $requireAuth();
    // Instantiate Services
    $avisRepo = new \App\Repositories\Impl\AvisRepository();
    $avisService = new \App\Services\AvisService($avisRepo);
    $reservationRepo = new \App\Repositories\Impl\ReservationRepository();

    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');

    try {
        // Verify reservation ownership
        $reservation = $reservationRepo->findById($reservationId);
        if (!$reservation || $reservation['id_user'] != $userId) {
            throw new Exception("Réservation invalide.");
        }

        // Create review
        $avisService->createReview($reservationId, $rating, $comment);
        $_SESSION['success'] = "Votre avis a été publié !";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: /reservations");
    exit;
});

$router->get('/admin/users', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    require BASE_PATH . 'src/views/admin/users.php';
});

$router->post('/admin/users/delete', function () use ($authService, $userService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    $userId = (int) ($_POST['user_id'] ?? 0);
    $userRepo = new UserRepository();
    $userRepo->delete($userId);

    $_SESSION['success'] = "Utilisateur supprimé.";
    header("Location: /admin/users");
    exit;
});

$router->get('/admin/reclamations', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    require BASE_PATH . 'src/views/admin/reclamations.php';
});

$router->post('/admin/reclamations/notify', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    $id = (int) ($_POST['id'] ?? 0);

    $recRepo = new ReclamationRepository();
    $reclamation = $recRepo->findById($id);

    if ($reclamation) {
        $notifRepo = new NotificationRepository();
        $notifService = new \App\Services\NotificationService($notifRepo);

        $hostId = $reclamation['id_owner'];
        $message = "Admin Alert: Vous avez une réclamation concernant un de vos logements (ID: " . $reclamation['id_log'] . "). Veuillez vérifier.";

        $notifService->notifyUser($hostId, 'admin_alert', $message);
        $_SESSION['success'] = "Notification envoyée à l'hôte.";
    } else {
        $_SESSION['error'] = "Réclamation introuvable.";
    }

    header("Location: /admin/reclamations");
    exit;
});

$router->get('/admin/logements', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    require BASE_PATH . 'src/views/admin/logements.php';
});

$router->post('/admin/logements/delete', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    $id = (int) ($_POST['id'] ?? 0);
    $logRepo = new \App\Repositories\Impl\LogementRepository();
    $logRepo->delete($id);

    $_SESSION['success'] = "Logement supprimé.";
    header("Location: /admin/logements");
    exit;
});

$router->post('/admin/reclamations/delete', function () use ($authService) {
    if ($authService->getUserRole() !== 'admin') {
        header("Location: /");
        exit;
    }
    $id = (int) ($_POST['id'] ?? 0);
    $repo = new \App\Repositories\Impl\ReclamationRepository();
    $repo->delete($id);

    $_SESSION['success'] = "Réclamation supprimée.";
    header("Location: /admin/reclamations");
    exit;
});

$router->resolve();


function dump_die($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();


}
