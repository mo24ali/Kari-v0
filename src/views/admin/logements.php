<?php
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/nav.php';

use App\Repositories\Impl\UserRepository;
use App\Repositories\Impl\LogementRepository;
use App\Repositories\Impl\ReservationRepository;
use App\Repositories\Impl\ImageRepository;
use App\Services\AdminService;

$userRole = $_SESSION['user_role'] ?? null;

if ($userRole !== 'admin') {
    header("Location: /");
    exit;
}

$userRepository = new UserRepository();
$logementRepository = new LogementRepository();
$reservationRepository = new ReservationRepository();
$imageRepository = new ImageRepository();

$adminService = new AdminService(
    $userRepository,
    $logementRepository,
    $reservationRepository,
    $imageRepository
);

$logements = $adminService->recent_logements ?? $logementRepository->findAll();
?>

<div class="min-h-screen bg-surface py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="/admin" class="text-secondary hover:text-primary mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Retour au tableau de bord
                </a>
                <h1 class="text-3xl font-bold text-primary">Gestion des logements</h1>
                <p class="text-secondary">Gérez les offres de location disponibles sur la plateforme</p>
            </div>
            <!-- Coming soon: Filters -->
        </div>

        <div class="bg-surface rounded-lg shadow-custom-lg border border-light overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 border-b border-light">
                            <th class="p-4 font-semibold text-secondary text-sm">Logement</th>
                            <th class="p-4 font-semibold text-secondary text-sm">Prix</th>
                            <th class="p-4 font-semibold text-secondary text-sm">Propriétaire</th>
                            <th class="p-4 font-semibold text-secondary text-sm">Adresse</th>
                            <th class="p-4 font-semibold text-secondary text-sm text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <?php if (empty($logements)): ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-secondary">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-home text-4xl mb-4 text-gray-300"></i>
                                        <p>Aucun logement trouvé</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logements as $logement): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($logement['primary_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($logement['primary_image']); ?>"
                                                    alt="Logement"
                                                    class="w-16 h-12 object-cover rounded-lg mr-4 border border-light">
                                            <?php else: ?>
                                                <div
                                                    class="w-16 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg mr-4 flex items-center justify-center">
                                                    <i class="fas fa-home text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="font-medium text-primary">Logement #
                                                    <?php echo $logement['id']; ?>
                                                </div>
                                                <div class="text-xs text-secondary mt-0.5">Ajouté le
                                                    <?php echo date('d/m/Y', strtotime('now')); // TODO: Add created_at to DB ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="font-bold text-primary">
                                            <?php echo number_format($logement['price'], 2); ?>€
                                        </span>
                                        <span class="text-xs text-secondary">/nuit</span>
                                    </td>
                                    <td class="p-4 text-sm">
                                        <div class="flex items-center">
                                            <div
                                                class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-xs mr-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-primary">
                                                    <?php echo htmlspecialchars($logement['firstname'] . ' ' . $logement['lastname']); ?>
                                                </span>
                                                <span class="text-xs text-secondary">
                                                    <?php echo htmlspecialchars($logement['owner_email']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-sm text-secondary truncate max-w-xs">
                                        <i class="fas fa-map-marker-alt mr-1.5 text-red-500"></i>
                                        <?php echo htmlspecialchars($logement['address']); ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="/hote?id=<?php echo $logement['id']; ?>" target="_blank"
                                                class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Voir">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button
                                                class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../partials/script.php';
require_once __DIR__ . '/../partials/footer.php';
?>