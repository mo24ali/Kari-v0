<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}



use App\core\Database;
$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];
var_dump($userId);
$query = "
    SELECT l.*
    FROM favoris f
    JOIN logement l ON f.id_log = l.id
    WHERE f.id_voy = ?
";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$favoris = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Mes favoris</h1>
    <?php if (empty($favoris)): ?>
        <div class="text-center text-lg text-gray-500 dark:text-gray-400 mt-10">
            Vous n'avez pas encore de logements favoris.<br>
            <a href="/" class="text-indigo-600 hover:underline dark:text-indigo-400">Découvrir des logements</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <?php foreach ($favoris as $logement): ?>
                <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-lg border border-light overflow-hidden flex flex-col">
                    <?php if (!empty($logement['primary_image'])): ?>
                        <img src="<?php echo htmlspecialchars($logement['primary_image']); ?>"
                            alt="Logement #<?php echo htmlspecialchars($logement['id']); ?>"
                            class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                            <i class="fas fa-home text-white text-4xl"></i>
                        </div>
                    <?php endif; ?>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl font-bold">Logement #<?php echo htmlspecialchars($logement['id']); ?></h3>
                            <span class="text-xl font-bold text-primary">
                                <?php echo number_format($logement['price'], 2); ?>€
                                <span class="text-sm font-normal text-secondary">/nuit</span>
                            </span>
                        </div>
                        <div class="text-secondary mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <?php echo htmlspecialchars($logement['address'] ?? 'Adresse non spécifiée'); ?>
                        </div>
                        <form method="POST" action="/favoris/remove" class="mt-auto w-full">
                            <input type="hidden" name="logement_id" value="<?php echo htmlspecialchars($logement['id']); ?>">
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 mt-2 bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 rounded hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors">
                                <i class="fas fa-heart-broken mr-2"></i>Retirer des favoris
                            </button>
                        </form>
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