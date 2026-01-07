<?php

namespace App\Controllers;

use App\Repositories\FavorisRepository;
use App\Services\FavorisService;

class FavorisController
{
    private FavorisService $favorisService;

    public function __construct()
    {
        $repo = new FavorisRepository();
        $this->favorisService = new FavorisService($repo);
    }

    public function add()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logementId = $_POST['logement_id'] ?? null;
            $userId = $_SESSION['user_id'];

            if ($logementId) {
                $this->favorisService->addToFavoris($userId, (int) $logementId);
            }

            $referer = $_SERVER['HTTP_REFERER'] ?? '/favoris';
            header("Location: $referer");
            exit;
        }
    }

    public function remove()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logementId = $_POST['logement_id'] ?? null;
            $userId = $_SESSION['user_id'];

            if ($logementId) {
                $this->favorisService->removeFromFavoris($userId, (int) $logementId);
            }

            $referer = $_SERVER['HTTP_REFERER'] ?? '/favoris';
            header("Location: $referer");
            exit;
        }
    }
}
