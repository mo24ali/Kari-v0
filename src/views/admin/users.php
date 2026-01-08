<?php
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/nav.php';

use App\Repositories\Impl\UserRepository;
use App\Repositories\Impl\LogementRepository;
use App\Repositories\Impl\ReservationRepository;
use App\Repositories\Impl\ImageRepository;
use App\Services\AdminService;

$userRole = $_SESSION['user_role'] ?? null;

// Only admins can access this page
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

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$users = $adminService->getAllUsers($limit, $offset);
$totalUsers = $userRepository->count();
$totalPages = ceil($totalUsers / $limit);
?>

<div class="min-h-screen bg-surface py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="/admin" class="text-secondary hover:text-primary mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Retour au tableau de bord
                </a>
                <h1 class="text-3xl font-bold text-primary">Gestion des utilisateurs</h1>
                <p class="text-secondary">Gérez les comptes utilisateurs, hôtes et administrateurs</p>
            </div>
            <button
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors shadow-custom-lg">
                <i class="fas fa-plus mr-2"></i>Ajouter un utilisateur
            </button>
        </div>

        <div class="bg-surface rounded-lg shadow-custom-lg border border-light overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 border-b border-light">
                            <th class="p-4 font-semibold text-secondary text-sm">Utilisateur</th>
                            <th class="p-4 font-semibold text-secondary text-sm">Rôle</th>
                            <th class="p-4 font-semibold text-secondary text-sm">Téléphone</th>
                            <th class="p-4 font-semibold text-secondary text-sm">Statut</th>
                            <th class="p-4 font-semibold text-secondary text-sm text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium mr-3">
                                            <?php echo strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-primary">
                                                <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
                                            </div>
                                            <div class="text-sm text-secondary">
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-3 py-1 text-xs rounded-full font-medium inline-flex items-center space-x-1 <?php
                                    echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-200' :
                                        ($user['role'] === 'host' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200' :
                                            'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200');
                                    ?>">
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <i class="fas fa-shield-alt text-xs mr-1"></i>
                                        <?php elseif ($user['role'] === 'host'): ?>
                                            <i class="fas fa-home text-xs mr-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-user text-xs mr-1"></i>
                                        <?php endif; ?>
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-secondary">
                                    <?php echo htmlspecialchars($user['phone'] ?? 'Non renseigné'); ?>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                        <span class="w-2 h-2 mr-1.5 rounded-full bg-green-500"></span>
                                        Actif
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button
                                            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button
                                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-light flex items-center justify-between">
                <p class="text-sm text-secondary">
                    Affichage de <span class="font-medium">
                        <?php echo $offset + 1; ?>
                    </span> à <span class="font-medium">
                        <?php echo min($offset + $limit, $totalUsers); ?>
                    </span> sur <span class="font-medium">
                        <?php echo $totalUsers; ?>
                    </span> utilisateurs
                </p>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>"
                            class="px-3 py-1 border border-light rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Précédent</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>"
                            class="px-3 py-1 border border-light rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Suivant</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../partials/script.php';
require_once __DIR__ . '/../partials/footer.php';
?>