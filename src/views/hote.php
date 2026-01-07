<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

use App\Repositories\LogementRepository;
use App\Repositories\ImageRepository;
use App\Services\LogementService;

$logementRepository = new LogementRepository();
$imageRepository = new imageRepository();
$logementService = new LogementService($logementRepository);

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;

if ($userRole !== 'host') {
    header("Location: /");
    exit;
}

$logements = $logementService->getLogementsByOwner($userId);

foreach ($logements as &$logement) {
    $images = $imageRepository->findByLogement($logement['id']);
    $logement['images'] = $images;
    if (empty($logement['primary_image']) && !empty($images)) {
        $logement['primary_image'] = $images[0]['image_path'];
    }
}
?>

<div class="min-h-screen bg-surface py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-primary mb-2">Mes Logements</h1>
                <p class="text-secondary">Gérez vos logements</p>
            </div>
            <button onclick="openAddModal()" 
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                <i class="fas fa-plus mr-2"></i>Ajouter un logement
            </button>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($logements)): ?>
            <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-12 text-center">
                <i class="fas fa-home text-6xl text-secondary mb-4"></i>
                <p class="text-xl text-secondary mb-2">Aucun logement pour le moment</p>
                <p class="text-secondary mb-6">Commencez par ajouter votre premier logement</p>
                <button onclick="openAddModal()" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter un logement
                </button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($logements as $logement): ?>
                    <div class="bg-surface rounded-lg shadow-custom-lg border border-light overflow-hidden hover-lift">
                        <?php if (!empty($logement['primary_image'])): ?>
                            <div class="w-full h-48 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($logement['primary_image']); ?>" 
                                     alt="Logement #<?php echo htmlspecialchars($logement['id']); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <i class="fas fa-home text-white text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-xl font-bold">Logement #<?php echo htmlspecialchars($logement['id']); ?></h3>
                                <span class="text-2xl font-bold text-primary">
                                    <?php echo number_format($logement['price'], 2); ?>€
                                    <span class="text-sm font-normal text-secondary">/nuit</span>
                                </span>
                            </div>
                            
                            <p class="text-secondary mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <?php echo htmlspecialchars($logement['address'] ?? 'Adresse non spécifiée'); ?>
                            </p>
                            
                            <div class="flex space-x-2 pt-4 border-t border-light">
                                <button onclick="openEditModal(<?php echo htmlspecialchars($logement['id']); ?>, '<?php echo htmlspecialchars(addslashes($logement['address'])); ?>', <?php echo htmlspecialchars($logement['price']); ?>)" 
                                        class="flex-1 px-4 py-2 bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/40 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Modifier
                                </button>
                                <form method="POST" action="/logement/delete" class="flex-1" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce logement ?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($logement['id']); ?>">
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>1






<div id="logementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold">Ajouter un logement</h2>
            <button onclick="closeModal()" class="text-secondary hover:text-primary">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="logementForm" method="POST" action="/logement/add" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" id="logementId" name="id">
            
            <div>
                <label for="modalAddress" class="block text-sm font-medium mb-2">Adresse</label>
                <input type="text" 
                       id="modalAddress" 
                       name="address" 
                       required
                       value="<?php echo htmlspecialchars($_SESSION['old']['address'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Ex: 123 Rue de la Paix, Paris">
            </div>
            
            <div>
                <label for="modalPrice" class="block text-sm font-medium mb-2">Prix par nuit (€)</label>
                <input type="number" 
                       id="modalPrice" 
                       name="price" 
                       required
                       min="1"
                       step="0.01"
                       value="<?php echo htmlspecialchars($_SESSION['old']['price'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="50">
            </div>
            
            <div>
                <label for="modalImages" class="block text-sm font-medium mb-2">Images</label>
                <input type="file" 
                       id="modalImages" 
                       name="images[]" 
                       multiple
                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                       class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                <p class="text-xs text-secondary mt-1">Vous pouvez sélectionner plusieurs images. La première sera utilisée comme image principale.</p>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" 
                        onclick="closeModal()"
                        class="px-6 py-2 border border-light rounded-lg hover:bg-surface transition-colors">
                    Annuler
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <span id="submitText">Ajouter</span>
                </button>
            </div>
        </form>
        <?php unset($_SESSION['old']); ?>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Ajouter un logement';
    document.getElementById('logementForm').action = '/logement/add';
    document.getElementById('logementId').value = '';
    document.getElementById('modalAddress').value = '';
    document.getElementById('modalPrice').value = '';
    document.getElementById('submitText').textContent = 'Ajouter';
    document.getElementById('logementModal').classList.remove('hidden');
    document.getElementById('logementModal').classList.add('flex');
}

function openEditModal(id, address, price) {
    document.getElementById('modalTitle').textContent = 'Modifier le logement';
    document.getElementById('logementForm').action = '/logement/update';
    document.getElementById('logementId').value = id;
    document.getElementById('modalAddress').value = address;
    document.getElementById('modalPrice').value = price;
    document.getElementById('submitText').textContent = 'Modifier';
    document.getElementById('logementModal').classList.remove('hidden');
    document.getElementById('logementModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('logementModal').classList.add('hidden');
    document.getElementById('logementModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('logementModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php
require_once 'partials/script.php';
require_once 'partials/footer.php';
?>