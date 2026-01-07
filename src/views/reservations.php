<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

use App\Repositories\ReservationRepository;
use App\Repositories\LogementRepository;
use App\Services\BookingService;

$userId = $_SESSION['user_id'];

$reservationRepository = new ReservationRepository();
$logementRepository = new LogementRepository();
$bookingService = new BookingService($reservationRepository, $logementRepository);

$reservations = $bookingService->getUserReservations($userId);
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-2">
            Mes réservations
        </h1>
        <div class="h-1.5 w-20 bg-primary mx-auto rounded-full"></div>
    </div>

    <?php if (empty($reservations)): ?>
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <div class="bg-gray-100 dark:bg-gray-800/50 p-12 rounded-full mb-6 transition-colors duration-300">
                <i class="far fa-calendar-times text-7xl text-gray-300 dark:text-gray-700"></i>
            </div>
            <p class="text-2xl font-bold text-gray-600 dark:text-gray-300">Vous n'avez pas encore de réservation</p>
            <a href="/" class="mt-4 text-primary font-bold hover:underline">
                Explorer les logements <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($reservations as $reservation):
                $primaryImage = null;
            ?>
                <div
                    class="group flex flex-col h-full bg-white dark:bg-gray-900 rounded-[2.5rem] overflow-hidden border border-transparent dark:border-gray-800 hover:shadow-2xl transition-all duration-500">

                    <div class="relative aspect-[4/5] overflow-hidden rounded-[2rem] m-2.5">
                        <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <i class="fas fa-home text-4xl text-gray-300 dark:text-gray-700"></i>
                        </div>
                    </div>

                    <div class="px-5 pb-6 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate">
                                <?= htmlspecialchars($reservation['address'] ?? "Logement"); ?>
                            </h3>
                        </div>

                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-2 flex items-center gap-1">
                            <i class="far fa-calendar-alt text-primary"></i>
                            Du <?= date('d/m/Y', strtotime($reservation['start_date'])); ?>
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 flex items-center gap-1">
                            <i class="far fa-calendar-check text-primary"></i>
                            Au <?= date('d/m/Y', strtotime($reservation['end_date'])); ?>
                        </p>

                        <div class="mt-auto pt-4 border-t dark:border-gray-800">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xl font-black text-gray-900 dark:text-white">
                                    <?= number_format($reservation['price'], 0, ',', ' '); ?> € <span
                                        class="text-xs font-normal text-gray-500">/ nuit</span>
                                </span>
                            </div>

                            <form method="POST" action="/reservation/cancel">
                                <!-- Note: Repository delete uses reservation ID, not log ID -->
                                <input type="hidden" name="reservation_id" value="<?= $reservation['id']; ?>">
                                <button type="submit"
                                    class="w-full bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all duration-300"
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                    <i class="fas fa-times-circle mr-2"></i> Annuler
                                </button>
                            </form>

                            <!-- Reclamation Button/Form -->
                            <div class="mt-2">
                                <button type="button"
                                    onclick="document.getElementById('reclamation-modal-<?= $reservation['id']; ?>').classList.remove('hidden')"
                                    class="w-full text-center text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-xs font-medium underline transition-colors">
                                    Signaler un problème
                                </button>
                            </div>

                            <!-- Modal for Reclamation -->
                            <div id="reclamation-modal-<?= $reservation['id']; ?>"
                                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all">
                                    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Signaler un problème</h3>
                                    <p class="text-sm text-gray-500 mb-4">Pour le logement :
                                        <?= htmlspecialchars($reservation['address'] ?? ''); ?></p>

                                    <form method="POST" action="/reclamation/create">
                                        <input type="hidden" name="logement_id" value="<?= $reservation['id_log']; ?>">

                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Votre
                                                message</label>
                                            <textarea name="message" required rows="4"
                                                class="w-full p-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none dark:text-white placeholder-gray-400"
                                                placeholder="Décrivez le problème rencontré..."></textarea>
                                        </div>

                                        <div class="flex justify-end gap-3">
                                            <button type="button"
                                                onclick="document.getElementById('reclamation-modal-<?= $reservation['id']; ?>').classList.add('hidden')"
                                                class="px-4 py-2 text-gray-600 dark:text-gray-400 font-bold hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                                Annuler
                                            </button>
                                            <button type="submit"
                                                class="px-6 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors shadow-lg shadow-primary/30">
                                                Envoyer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'partials/script.php';
require_once 'partials/footer.php';
?>