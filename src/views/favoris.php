<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

use App\Repositories\FavorisRepository;
use App\Services\FavorisService;

$userId = $_SESSION['user_id'];

$favorisRepo = new FavorisRepository();
$favorisService = new FavorisService($favorisRepo);

$favoris = $favorisService->getUserFavoris($userId);
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold mb-2 text-primary">Mes favoris</h1>
        <p class="text-secondary">Retrouvez ici vos coups de cœur</p>
    </div>

    <?php if (empty($favoris)): ?>
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <i class="fas fa-heart-broken text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg text-secondary mb-4">Vous n'avez pas encore de favoris.</p>
            <a href="/"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-blue-700 transition-colors">
                Explorer les logements
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($favoris as $logement): ?>
                <div
                    class="group bg-surface rounded-xl shadow-custom border border-light overflow-hidden flex flex-col hover:shadow-custom-lg transition-shadow duration-300">
                    <div class="relative aspect-video overflow-hidden">
                        <?php if (!empty($logement['primary_image'])): ?>
                            <img src="<?php echo htmlspecialchars($logement['primary_image']); ?>" alt="Logement"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div
                                class="w-full h-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <i class="fas fa-home text-white text-4xl opacity-50"></i>
                            </div>
                        <?php endif; ?>

                        <div class="absolute top-2 right-2">
                            <form method="POST" action="/favoris/remove">
                                <input type="hidden" name="logement_id"
                                    value="<?php echo htmlspecialchars($logement['id']); ?>">
                                <button type="submit"
                                    class="p-2 bg-white/90 dark:bg-gray-900/90 rounded-full text-red-500 hover:text-red-600 hover:bg-white shadow-sm transition-all transform hover:scale-110"
                                    title="Retirer des favoris">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg truncate flex-1 mr-2">
                                <?php echo htmlspecialchars($logement['address'] ?? 'Logement'); ?></h3>
                            <span class="font-bold text-primary whitespace-nowrap">
                                <?php echo number_format($logement['price'], 0); ?>€
                                <span class="text-xs font-normal text-secondary">/nuit</span>
                            </span>
                        </div>

                        <div class="mt-auto pt-4 flex items-center justify-between">
                            <a href="/hote?id=<?php echo $logement['id']; ?>"
                                class="text-sm font-medium text-primary hover:underline">
                                Voir détails
                            </a>
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