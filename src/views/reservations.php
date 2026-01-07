<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';
use App\Public;
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

use App\core\Database;
use App\Repositories\ImageRepository;
use App\Repositories\ReservationRepository;

use function App\dump_die;

$db = Database::getInstance()->getConnection();
$reservationRepository = new ReservationRepository();
$userId = $_SESSION['user_id'];
$query = "
    SELECT l.*
    FROM logement l
    JOIN reservation r ON r.id_log = l.id
    WHERE r.id_user = ?
";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// dump_die($reservation);
foreach ($reservation as $logement) {
    // dump_die($logement);
    $images = $reservationRepository->findByLogement($logement['id']);
    if (empty($logement['primary_image']) && !empty($images)) {
        $logement['primary_image'] = $images[0]['image_path'];
    }
}
unset($logement);
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-2">
            Mes reservation
        </h1>
        <div class="h-1.5 w-20 bg-primary mx-auto rounded-full"></div>
    </div>

    <?php if (empty($reservation)): ?>
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <div class="bg-gray-100 dark:bg-gray-800/50 p-12 rounded-full mb-6 transition-colors duration-300">
                <i class="far fa-heart text-7xl text-gray-300 dark:text-gray-700"></i>
            </div>
            <p class="text-2xl font-bold text-gray-600 dark:text-gray-300">Vous n'avez pas encore de reservation</p>
            <a href="/" class="mt-4 text-primary font-bold hover:underline">
                Explorer les logements <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($reservation as $logement): ?>
                <div class="group flex flex-col h-full bg-white dark:bg-gray-900 rounded-[2.5rem] overflow-hidden border border-transparent dark:border-gray-800 hover:shadow-2xl transition-all duration-500">
                    
                    <div class="relative aspect-[4/5] overflow-hidden rounded-[2rem] m-2.5">
                        <?php if (!empty($logement['primary_image'])): ?>
                            <img src="<?= htmlspecialchars($logement['primary_image']); ?>"
                                 alt="Logement"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-300 dark:text-gray-700"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="px-5 pb-6 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate">
                                <?= htmlspecialchars($logement['address'] ?? "Magnifique Logement"); ?>
                            </h3>
                        </div>
                        
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 flex items-center gap-1">
                            <i class="fas fa-map-marker-alt text-[10px]"></i>
                            <?= htmlspecialchars($logement['address'] ?? 'France'); ?>
                        </p>

                        <div class="mt-auto pt-4 border-t dark:border-gray-800">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xl font-black text-gray-900 dark:text-white">
                                    <?= number_format($logement['price'], 0, ',', ' '); ?> € <span class="text-xs font-normal text-gray-500">/ nuit</span>
                                </span>
                            </div>

                            <form method="POST" action="/reservation/remove">
                                <input type="hidden" name="logement_id" value="<?= $logement['id']; ?>">
                                <button type="submit" class="w-full bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all duration-300">
                                    <i class="fas fa-heart-broken mr-2"></i> Retirer
                                </button>
                            </form>
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