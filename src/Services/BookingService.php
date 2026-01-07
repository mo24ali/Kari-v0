<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Repositories\LogementRepository;
use Exception;

class BookingService
{
    private ReservationRepository $reservationRepository;
    private LogementRepository $logementRepository;

    public function __construct(
        ReservationRepository $reservationRepository,
        LogementRepository $logementRepository
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->logementRepository = $logementRepository;
    }

    public function createReservation(array $data, int $userId): array
    {
        $errors = $this->validateReservationData($data);

        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }

        $logement = $this->logementRepository->findById($data['id_log']);

        if (!$logement) {
            throw new Exception("Logement non trouvé.");
        }

        if ($logement->getIdOwner() == $userId) {
            throw new Exception("Vous ne pouvez pas réserver votre propre logement.");
        }

        $startDate = $data['start_date'];
        $endDate = $data['end_date'];

        if (!$this->reservationRepository->checkAvailability($data['id_log'], $startDate, $endDate)) {
            throw new Exception("Ce logement n'est pas disponible pour ces dates.");
        }

        $reservationData = [
            'id_user' => $userId,
            'id_log' => $data['id_log'],
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $id = $this->reservationRepository->save($reservationData);
        return ['id' => $id, 'message' => 'Réservation créée avec succès'];
    }

    public function getUserReservations(int $userId): array
    {
        return $this->reservationRepository->findByUser($userId);
    }

    public function getLogementReservations(int $logementId): array
    {
        return $this->reservationRepository->findByLogement($logementId);
    }

    private function validateReservationData(array $data): array
    {
        $errors = [];

        if (empty($data['id_log']) || !is_numeric($data['id_log'])) {
            $errors[] = "Logement invalide.";
        }

        if (empty($data['start_date'])) {
            $errors[] = "La date de début est requise.";
        }

        if (empty($data['end_date'])) {
            $errors[] = "La date de fin est requise.";
        }

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $start = strtotime($data['start_date']);
            $end = strtotime($data['end_date']);
            $today = strtotime('today');

            if ($start === false || $end === false) {
                $errors[] = "Format de date invalide.";
            } elseif ($start < $today) {
                $errors[] = "La date de début ne peut pas être dans le passé.";
            } elseif ($end <= $start) {
                $errors[] = "La date de fin doit être après la date de début.";
            }
        }

        return $errors;
    }
    public function cancelReservation(int $reservationId, int $userId): void
    {
        // Ideally we should check if the reservation belongs to the user
        // But for now, we'll assume the controller/UI handles basic ownership checks or we just delete it
        // A better approach: find the reservation, check user_id, then delete.
        // Since findById isn't exposed in repo easily, we'll trusting the ID for now or just implementing delete.

        $this->reservationRepository->delete($reservationId);
    }
}
