<?php
$title = "Gestion Logements - Admin KARI";
ob_start();

use App\Repositories\Impl\LogementRepository;
$logementRepo = new LogementRepository();
$logements = $logementRepo->findAll();
?>

<div class="px-4 py-8 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Gestion des Logements</h1>
        <a href="/admin" class="text-gray-600 hover:text-gray-900">Retour Dashboard</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logement
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hôte</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix/Nuit
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($logements as $log): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <?php if ($log->getPrimaryImage()): ?>
                                        <img class="h-10 w-10 rounded-lg object-cover"
                                            src="<?= htmlspecialchars($log->getPrimaryImage()) ?>" alt="">
                                    <?php else: ?>
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-home text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($log->getAddress() ?? 'Sans adresse') ?></div>
                                    <div class="text-sm text-gray-500">ID: #<?= $log->getId() ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php
                                $owner = $log->getOwner();
                                echo htmlspecialchars(($owner['firstname'] ?? '') . ' ' . ($owner['lastname'] ?? ''));
                                ?>
                            </div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($owner['email'] ?? '') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= number_format($log->getPrice(), 2) ?> €
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="/admin/logements/delete" method="POST" class="inline"
                                onsubmit="return confirm('Supprimer ce logement DEFINITIVEMENT ?');">
                                <input type="hidden" name="id" value="<?= $log->getId() ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>