<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

use App\Repositories\Impl\UserRepository;
use App\Repositories\LogementRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\ImageRepository;
use App\Services\AdminService;

$userRole = $_SESSION['user_role'] ?? null;

// Only admins can access this page
if ($userRole !== 'admin') {
    header("Location: /");
    exit;
}

$userRepository = new \App\Repositories\Impl\UserRepository();
$logementRepository = new \App\Repositories\LogementRepository();
$reservationRepository = new \App\Repositories\ReservationRepository();
$imageRepository = new \App\Repositories\ImageRepository();

$adminService = new \App\Services\AdminService(
    $userRepository,
    $logementRepository,
    $reservationRepository,
    $imageRepository
);

$stats = $adminService->getDashboardStats();
$recentUsers = array_slice($stats['recent_users'], 0, 5);
$recentLogements = array_slice($stats['recent_logements'], 0, 5);
?>

<div class="min-h-screen bg-surface py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">Tableau de bord administrateur</h1>
            <p class="text-secondary">Vue d'ensemble de la plateforme KARI</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary mb-1">Total Utilisateurs</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-secondary">Hôtes: <?php echo $stats['total_hosts']; ?></span>
                    <span class="mx-2 text-secondary">•</span>
                    <span class="text-secondary">Voyageurs: <?php echo $stats['total_travellers']; ?></span>
                </div>
            </div>

            <!-- Total Logements -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary mb-1">Total Logements</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['total_logements']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                        <i class="fas fa-home text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-secondary">Offres disponibles</span>
                </div>
            </div>

            <!-- Total Reservations -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary mb-1">Réservations</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['total_reservations']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/20 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-secondary">Réservations totales</span>
                </div>
            </div>

            <!-- Total Reviews -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary mb-1">Avis</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['total_reviews']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/20 flex items-center justify-center">
                        <i class="fas fa-star text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-secondary">Avis clients</span>
                </div>
            </div>
        </div>

        <!-- Charts and Additional Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Users by Role Chart -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <h3 class="text-xl font-bold mb-4">Répartition des utilisateurs</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Hôtes</span>
                            <span class="text-sm text-secondary"><?php echo $stats['users_by_role']['host']; ?></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: <?php echo $stats['total_users'] > 0 ? ($stats['users_by_role']['host'] / $stats['total_users'] * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Voyageurs</span>
                            <span class="text-sm text-secondary"><?php echo $stats['users_by_role']['traveller']; ?></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" 
                                 style="width: <?php echo $stats['total_users'] > 0 ? ($stats['users_by_role']['traveller'] / $stats['total_users'] * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Administrateurs</span>
                            <span class="text-sm text-secondary"><?php echo $stats['users_by_role']['admin']; ?></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" 
                                 style="width: <?php echo $stats['total_users'] > 0 ? ($stats['users_by_role']['admin'] / $stats['total_users'] * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <h3 class="text-xl font-bold mb-4">Statistiques supplémentaires</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-heart text-red-500 mr-3"></i>
                            <span class="font-medium">Favoris</span>
                        </div>
                        <span class="text-xl font-bold"><?php echo number_format($stats['total_favoris']); ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-image text-blue-500 mr-3"></i>
                            <span class="font-medium">Images</span>
                        </div>
                        <span class="text-xl font-bold"><?php echo number_format($stats['total_images']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Utilisateurs récents</h3>
                    <a href="/admin/users" class="text-sm text-primary hover:underline">Voir tout</a>
                </div>
                <div class="space-y-3">
                    <?php if (empty($recentUsers)): ?>
                        <p class="text-secondary text-center py-4">Aucun utilisateur</p>
                    <?php else: ?>
                        <?php foreach ($recentUsers as $user): ?>
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium mr-3">
                                        <?php echo strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-medium"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></p>
                                        <p class="text-sm text-secondary"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full <?php 
                                    echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-200' : 
                                        ($user['role'] === 'host' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200' : 
                                        'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200'); 
                                ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Logements -->
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Logements récents</h3>
                    <a href="/admin/logements" class="text-sm text-primary hover:underline">Voir tout</a>
                </div>
                <div class="space-y-3">
                    <?php if (empty($recentLogements)): ?>
                        <p class="text-secondary text-center py-4">Aucun logement</p>
                    <?php else: ?>
                        <?php foreach ($recentLogements as $logement): ?>
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                <div class="flex items-center flex-1">
                                    <?php if (!empty($logement['primary_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($logement['primary_image']); ?>" 
                                             alt="Logement #<?php echo $logement['id']; ?>"
                                             class="w-16 h-16 rounded-lg object-cover mr-3">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center mr-3">
                                            <i class="fas fa-home text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <p class="font-medium">Logement #<?php echo htmlspecialchars($logement['id']); ?></p>
                                        <p class="text-sm text-secondary truncate"><?php echo htmlspecialchars($logement['address'] ?? 'Adresse non spécifiée'); ?></p>
                                        <p class="text-sm font-bold text-primary mt-1"><?php echo number_format($logement['price'], 2); ?>€ /nuit</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'partials/script.php';
require_once 'partials/footer.php';
?>
