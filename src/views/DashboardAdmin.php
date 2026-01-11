<?php
$title = "Dashboard Admin - KARI";
ob_start();

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

$stats = $adminService->getDashboardStats();
$recentUsers = array_slice($stats['recent_users'], 0, 5);
$recentLogements = array_slice($stats['recent_logements'], 0, 5);
?>

<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400 mb-3">
                Tableau de bord
            </h1>
            <p class="text-gray-400 text-lg">Vue d'ensemble de la plateforme KARI</p>
        </div>

        <!-- Statistics Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            <!-- Total Users Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 hover:bg-gray-800/70 transition-all duration-300 border border-gray-700/50 hover:border-gray-600/50 hover:-translate-y-1 shadow-xl stagger-1 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Utilisateurs</p>
                        <p class="text-4xl font-black text-white"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs font-bold">
                    <span class="px-3 py-1 bg-blue-900/30 text-blue-300 rounded-full border border-blue-800/50">
                        Hôtes: <?php echo $stats['total_hosts']; ?>
                    </span>
                    <span class="px-3 py-1 bg-emerald-900/30 text-emerald-300 rounded-full border border-emerald-800/50">
                        Voyageurs: <?php echo $stats['total_travellers']; ?>
                    </span>
                </div>
            </div>

            <!-- Total Logements Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 hover:bg-gray-800/70 transition-all duration-300 border border-gray-700/50 hover:border-gray-600/50 hover:-translate-y-1 shadow-xl stagger-2 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-home text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Logements</p>
                        <p class="text-4xl font-black text-white"><?php echo number_format($stats['total_logements']); ?></p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 font-medium">Offres disponibles sur la plateforme</p>
            </div>

            <!-- Total Reservations Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 hover:bg-gray-800/70 transition-all duration-300 border border-gray-700/50 hover:border-gray-600/50 hover:-translate-y-1 shadow-xl stagger-3 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/20">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Réservations</p>
                        <p class="text-4xl font-black text-white"><?php echo number_format($stats['total_reservations']); ?></p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 font-medium">Réservations totales effectuées</p>
            </div>

            <!-- Total Revenue Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 hover:bg-gray-800/70 transition-all duration-300 border border-gray-700/50 hover:border-gray-600/50 hover:-translate-y-1 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <i class="fas fa-hand-holding-usd text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Revenu Total</p>
                        <p class="text-4xl font-black text-emerald-400"><?php echo number_format($stats['total_revenue'], 2); ?>€</p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 font-medium">Revenu généré par la plateforme</p>
            </div>

            <!-- Reserved Logements Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 hover:bg-gray-800/70 transition-all duration-300 border border-gray-700/50 hover:border-gray-600/50 hover:-translate-y-1 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/20">
                        <i class="fas fa-bookmark text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Logements Réservés</p>
                        <p class="text-4xl font-black text-white"><?php echo number_format($stats['total_reserved_logements']); ?></p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 font-medium">Logements avec réservations actives</p>
            </div>

            <!-- Total Reviews Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 hover:bg-gray-800/70 transition-all duration-300 border border-gray-700/50 hover:border-gray-600/50 hover:-translate-y-1 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center shadow-lg shadow-yellow-500/20">
                        <i class="fas fa-star text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Avis</p>
                        <p class="text-4xl font-black text-white"><?php echo number_format($stats['total_reviews']); ?></p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 font-medium">Avis clients publiés</p>
            </div>
        </div>

        <!-- User Distribution & Additional Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <!-- Users by Role -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-8 border border-gray-700/50 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <h3 class="text-2xl font-black mb-6 text-white">Répartition des utilisateurs</h3>
                <div class="space-y-5">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold flex items-center gap-2 text-gray-300">
                                <i class="fas fa-home text-blue-400"></i>
                                Hôtes
                            </span>
                            <span class="text-sm font-black text-white"><?php echo $stats['users_by_role']['host']; ?></span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-1000 shadow-lg shadow-blue-500/20"
                                style="width: <?php echo $stats['total_users'] > 0 ? ($stats['users_by_role']['host'] / $stats['total_users'] * 100) : 0; ?>%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold flex items-center gap-2 text-gray-300">
                                <i class="fas fa-plane text-emerald-400"></i>
                                Voyageurs
                            </span>
                            <span class="text-sm font-black text-white"><?php echo $stats['users_by_role']['traveller']; ?></span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-3 rounded-full transition-all duration-1000 shadow-lg shadow-emerald-500/20"
                                style="width: <?php echo $stats['total_users'] > 0 ? ($stats['users_by_role']['traveller'] / $stats['total_users'] * 100) : 0; ?>%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold flex items-center gap-2 text-gray-300">
                                <i class="fas fa-crown text-purple-400"></i>
                                Administrateurs
                            </span>
                            <span class="text-sm font-black text-white"><?php echo $stats['users_by_role']['admin']; ?></span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-1000 shadow-lg shadow-purple-500/20"
                                style="width: <?php echo $stats['total_users'] > 0 ? ($stats['users_by_role']['admin'] / $stats['total_users'] * 100) : 0; ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-8 border border-gray-700/50 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <h3 class="text-2xl font-black mb-6 text-white">Statistiques supplémentaires</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-800/50 to-gray-800/30 rounded-2xl border border-gray-700/50 hover:border-gray-600/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/20">
                                <i class="fas fa-heart text-white text-xl"></i>
                            </div>
                            <span class="font-bold text-gray-200">Favoris</span>
                        </div>
                        <span class="text-3xl font-black text-white"><?php echo number_format($stats['total_favoris']); ?></span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-800/50 to-gray-800/30 rounded-2xl border border-gray-700/50 hover:border-gray-600/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/20">
                                <i class="fas fa-image text-white text-xl"></i>
                            </div>
                            <span class="font-bold text-gray-200">Images</span>
                        </div>
                        <span class="text-3xl font-black text-white"><?php echo number_format($stats['total_images']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <!-- Recent Users -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-8 border border-gray-700/50 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-white">Utilisateurs récents</h3>
                    <a href="/admin/users"
                        class="text-sm font-bold text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-2 group">
                        Voir tout <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    <?php if (empty($recentUsers)): ?>
                        <p class="text-gray-400 text-center py-8">Aucun utilisateur</p>
                    <?php else: ?>
                        <?php foreach ($recentUsers as $user): ?>
                            <div class="flex items-center justify-between p-4 hover:bg-gray-800/70 rounded-2xl transition-all duration-300 group border border-transparent hover:border-gray-600/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-sm font-black shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                                        <?php echo strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">
                                            <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
                                        </p>
                                        <p class="text-xs text-gray-400"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 text-xs font-black rounded-full <?php
                                echo $user['role'] === 'admin' ? 'bg-purple-900/30 text-purple-300 border border-purple-800/50' :
                                    ($user['role'] === 'host' ? 'bg-blue-900/30 text-blue-300 border border-blue-800/50' :
                                        'bg-emerald-900/30 text-emerald-300 border border-emerald-800/50');
                                ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Logements -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-8 border border-gray-700/50 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-white">Logements récents</h3>
                    <a href="/admin/logements"
                        class="text-sm font-bold text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-2 group">
                        Voir tout <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    <?php if (empty($recentLogements)): ?>
                        <p class="text-gray-400 text-center py-8">Aucun logement</p>
                    <?php else: ?>
                        <?php foreach ($recentLogements as $logement): ?>
                            <div class="flex items-center gap-4 p-4 hover:bg-gray-800/70 rounded-2xl transition-all duration-300 group border border-transparent hover:border-gray-600/50">
                                <?php if ($logement->getPrimaryImage()): ?>
                                    <img src="<?php echo htmlspecialchars($logement->getPrimaryImage()); ?>"
                                        alt="Logement #<?php echo $logement->getId(); ?>"
                                        class="w-20 h-20 rounded-2xl object-cover shadow-lg group-hover:scale-105 transition-transform border border-gray-700/50">
                                <?php else: ?>
                                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg border border-gray-700/50">
                                        <i class="fas fa-home text-white text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <a href="/logement/details?id=<?php echo $logement->getId(); ?>" class="hover:underline">
                                        <p class="font-bold text-white">Logement #<?php echo htmlspecialchars($logement->getId()); ?></p>
                                    </a>
                                    <p class="text-xs text-gray-400 truncate">
                                        <?php echo htmlspecialchars($logement->getAddress() ?? 'Adresse non spécifiée'); ?>
                                    </p>
                                    <p class="text-sm font-black text-emerald-400 mt-1">
                                        <?php echo number_format($logement->getPrice(), 2); ?>€ <span class="text-xs font-normal text-gray-400">/nuit</span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl p-8 border border-gray-700/50 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-black text-white">Réservations récentes</h3>
                <a href="/admin/reservations"
                    class="text-sm font-bold text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-2 group">
                    Voir tout <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            <div class="overflow-x-auto rounded-2xl border border-gray-700/50">
                <table class="w-full text-left">
                    <thead class="bg-gray-800/70">
                        <tr>
                            <th class="py-4 px-6 font-black text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/50">Voyageur</th>
                            <th class="py-4 px-6 font-black text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/50">Logement</th>
                            <th class="py-4 px-6 font-black text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/50">Dates</th>
                            <th class="py-4 px-6 font-black text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/50">Prix Total</th>
                            <th class="py-4 px-6 font-black text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/50 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        <?php if (empty($stats['recent_reservations'])): ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">Aucune réservation récente</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['recent_reservations'] as $res): ?>
                                <?php
                                $start = new DateTime($res['start_date']);
                                $end = new DateTime($res['end_date']);
                                $days = $start->diff($end)->days + 1;
                                $total = $days * $res['price'];
                                ?>
                                <tr class="hover:bg-gray-800/70 transition-colors">
                                    <td class="py-5 px-6">
                                        <p class="font-bold text-white">
                                            <?php echo htmlspecialchars($res['firstname'] . ' ' . $res['lastname']); ?></p>
                                    </td>
                                    <td class="py-5 px-6">
                                        <p class="text-sm text-gray-400 truncate max-w-xs">
                                            <?php echo htmlspecialchars($res['address']); ?></p>
                                    </td>
                                    <td class="py-5 px-6">
                                        <p class="text-sm font-medium text-white"><?php echo $start->format('d/m/Y'); ?> -
                                            <?php echo $end->format('d/m/Y'); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo $days; ?> nuit(s)</p>
                                    </td>
                                    <td class="py-5 px-6 font-black text-emerald-400">
                                        <?php echo number_format($total, 2); ?>€
                                    </td>
                                    <td class="py-5 px-6 text-right">
                                        <a href="/receipt?id=<?php echo $res['id']; ?>"
                                            class="text-blue-400 hover:text-blue-300 text-sm font-bold transition-colors inline-flex items-center gap-1 group">
                                            Voir reçu <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                        </a>
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
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>