<?php

namespace App\Controllers;

use App\Repositories\ReservationRepository;
use App\Repositories\LogementRepository;
use App\Services\BookingService;
use Exception;
class ReservationController
{
    private BookingService $bookingService;

    public function __construct()
    {
        $resRepo = new ReservationRepository();
        $logRepo = new LogementRepository();
        $this->bookingService = new BookingService($resRepo, $logRepo);
    }

    public function cancel()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservationId = $_POST['reservation_id'] ?? null;
            $userId = $_SESSION['user_id'];

            if ($reservationId) {
                try {
                    $this->bookingService->cancelReservation((int) $reservationId, $userId);
                } catch (Exception $e) {
                    
                }
            }

            header('Location: /reservations');
            exit;
        }
    }
}
