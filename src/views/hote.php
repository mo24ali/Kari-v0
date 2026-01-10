<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

use App\Repositories\Impl\LogementRepository;
use App\Repositories\Impl\ImageRepository;
use App\Services\LogementService;

$logementRepository = new LogementRepository();
$imageRepository = new ImageRepository();
$logementService = new LogementService($logementRepository);

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;

if ($userRole !== 'host' && $userRole !== 'admin') {
    $_SESSION['error'] = "Erreur : your role doesn't allow you here";

    header("Location: /");
    exit;
}

$logements = $logementService->getLogementsByOwner($userId);


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
        <!-- affichage message d'erreur  -->
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
                        <?php if ($logement->getPrimaryImage()): ?>
                            <div class="w-full h-48 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($logement->getPrimaryImage()); ?>"
                                    alt="Logement #<?php echo htmlspecialchars($logement->getId()); ?>"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <i class="fas fa-home text-white text-4xl"></i>
                            </div>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-xl font-bold">Logement #<?php echo htmlspecialchars($logement->getId()); ?></h3>
                                <span class="text-2xl font-bold text-primary">
                                    <?php echo number_format($logement->getPrice(), 2); ?>€
                                    <span class="text-sm font-normal text-secondary">/nuit</span>
                                </span>
                            </div>

                            <p class="text-secondary mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <?php echo htmlspecialchars($logement->getAddress() ?? 'Adresse non spécifiée'); ?>
                            </p>

                            <div class="flex space-x-2 pt-4 border-t border-light">
                                <button
                                    onclick='openEditModal(<?= $logement->getId() ?>, <?= json_encode($logement->getAddress() ?? "") ?>, <?= (float) $logement->getPrice() ?>)'
                                    class="flex-1 px-4 py-2 bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/40 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Modifier
                                </button>
                                <form method="POST" action="/logement/delete" class="flex-1"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce logement ?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($logement->getId()); ?>">
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

        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                Réclamations reçues
            </h2>
            <?php
            use App\Repositories\Impl\ReclamationRepository;
            use App\Services\ReclamationService;
            $reclamationRepo = new ReclamationRepository();
            $reclamationService = new ReclamationService($reclamationRepo);
            $reclamations = $reclamationService->getReclamationsForHost($userId);
            ?>

            <?php if (empty($reclamations)): ?>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center border border-gray-100 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">Aucune réclamation pour le moment. Tout va bien !</p>
                </div>
            <?php else: ?>
                <div class="grid gap-4">
                    <?php foreach ($reclamations as $reclamation): ?>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border-l-4 border-red-500 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($reclamation['address']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        De: <?= htmlspecialchars($reclamation['firstname'] . ' ' . $reclamation['lastname']); ?>
                                    </p>
                                </div>
                                <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">
                                    <?= date('d/m/Y H:i', strtotime($reclamation['created_at'])); ?>
                                </span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-gray-700 dark:text-gray-300 italic">
                                    "<?= nl2br(htmlspecialchars($reclamation['message'])); ?>"
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>






<!-- Improved Modal with premium design and animations -->
<div id="logementModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300 pointer-events-none">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div id="modalContent"
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <!-- Modal Header -->
            <div
                class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
                <div>
                    <h2 id="modalTitle" class="text-2xl font-black text-gray-900 dark:text-white">Ajouter un logement
                    </h2>
                    <p class="text-xs text-secondary mt-1">Veuillez remplir les informations suivantes</p>
                </div>
                <button onclick="closeModal()"
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="logementForm" method="POST" action="/logement/add" enctype="multipart/form-data"
                class="p-8 space-y-6">
                <input type="hidden" id="logementId" name="id">

                <div class="space-y-2">
                    <label for="modalAddress" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-map-marker-alt mr-2 text-primary"></i>Adresse complète
                    </label>
                    <input type="text" id="modalAddress" name="address" required
                        value="<?php echo htmlspecialchars($_SESSION['old']['address'] ?? ''); ?>"
                        class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-2xl focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all dark:text-white"
                        placeholder="Ex: 123 Rue de la Paix, Paris">
                </div>

                <div class="space-y-2">
                    <label for="modalPrice" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-euro-sign mr-2 text-primary"></i>Prix par nuit
                    </label>
                    <div class="relative">
                        <input type="number" id="modalPrice" name="price" required min="1" step="0.01"
                            value="<?php echo htmlspecialchars($_SESSION['old']['price'] ?? ''); ?>"
                            class="w-full pl-5 pr-12 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-2xl focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all dark:text-white"
                            placeholder="75.00">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">€</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-images mr-2 text-primary"></i>Photos du logement
                    </label>
                    <div class="relative group">
                        <input type="file" id="modalImages" name="images[]" multiple accept="image/*"
                            onchange="handleImagePreview(this)"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div
                            class="p-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl text-center group-hover:border-primary transition-colors bg-gray-50/50 dark:bg-gray-900/10">
                            <i
                                class="fas fa-cloud-upload-alt text-3xl text-gray-300 dark:text-gray-600 mb-2 group-hover:text-primary transition-colors"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cliquez ou glissez vos photos ici</p>
                            <p class="text-[10px] text-gray-400 mt-1">PNG, JPG ou WebP (max 5MB)</p>
                        </div>
                    </div>
                    <!-- Image Preview Container -->
                    <div id="imagePreviewContainer" class="grid grid-cols-4 gap-2 mt-4"></div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center gap-4 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-6 py-4 border-2 border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-[2] px-6 py-4 bg-primary text-white font-black rounded-2xl hover:bg-primary-dark shadow-xl shadow-primary/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i>
                        <span id="submitText">Ajouter le logement</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-active {
        display: block !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .modal-content-active {
        animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .preview-card {
        aspect-ratio: 1;
        position: relative;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .preview-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script>
    function handleImagePreview(input) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';

        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'preview-card group';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-check text-white"></i>
                        </div>
                    `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function openAddModal() {
        const modal = document.getElementById('logementModal');
        const content = document.getElementById('modalContent');
        const form = document.getElementById('logementForm');

        document.getElementById('modalTitle').textContent = 'Ajouter un logement';
        form.action = '/logement/add';
        document.getElementById('logementId').value = '';
        document.getElementById('modalAddress').value = '';
        document.getElementById('modalPrice').value = '';
        document.getElementById('imagePreviewContainer').innerHTML = '';
        document.getElementById('submitText').textContent = 'Ajouter le logement';

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function openEditModal(id, address, price) {
        const modal = document.getElementById('logementModal');
        const content = document.getElementById('modalContent');
        const form = document.getElementById('logementForm');

        document.getElementById('modalTitle').textContent = 'Modifier le logement';
        form.action = '/logement/update';
        document.getElementById('logementId').value = id;
        document.getElementById('modalAddress').value = address;
        document.getElementById('modalPrice').value = price;
        document.getElementById('imagePreviewContainer').innerHTML = '';
        document.getElementById('submitText').textContent = 'Enregistrer les modifications';

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModal() {
        const modal = document.getElementById('logementModal');
        const content = document.getElementById('modalContent');

        modal.style.opacity = '0';
        content.style.transform = 'scale(0.95)';

        setTimeout(() => {
            modal.classList.remove('modal-active');
            content.classList.remove('modal-content-active');
            modal.style.opacity = '';
            content.style.transform = '';
        }, 300);
    }

    // Close modal when clicking outside
    document.getElementById('logementModal').addEventListener('click', function (e) {
        if (e.target === this || e.target.classList.contains('bg-gray-900/60')) {
            closeModal();
        }
    });
</script>

<?php
require_once 'partials/script.php';
require_once 'partials/footer.php';
?>