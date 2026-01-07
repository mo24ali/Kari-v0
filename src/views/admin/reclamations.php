<?php
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/nav.php';

use App\Repositories\ReclamationRepository;
use App\Services\ReclamationService;

$repo = new ReclamationRepository();
$service = new ReclamationService($repo);
$reclamations = $service->getAllReclamations();
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Gestion des Réclamations</h1>
        <p class="text-gray-500">Vue d'ensemble de toutes les réclamations de la plateforme.</p>
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="p-5 font-bold text-gray-500 dark:text-gray-300 text-sm uppercase tracking-wider">Date
                        </th>
                        <th class="p-5 font-bold text-gray-500 dark:text-gray-300 text-sm uppercase tracking-wider">
                            Voyageur</th>
                        <th class="p-5 font-bold text-gray-500 dark:text-gray-300 text-sm uppercase tracking-wider">Hôte
                        </th>
                        <th class="p-5 font-bold text-gray-500 dark:text-gray-300 text-sm uppercase tracking-wider">
                            Logement</th>
                        <th class="p-5 font-bold text-gray-500 dark:text-gray-300 text-sm uppercase tracking-wider">
                            Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($reclamations)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Aucune réclamation enregistrée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reclamations as $reclamation): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="p-5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($reclamation['created_at'])); ?>
                                </td>
                                <td class="p-5">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($reclamation['firstname'] . ' ' . $reclamation['lastname']); ?>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <?= htmlspecialchars(($reclamation['owner_firstname'] ?? '') . ' ' . ($reclamation['owner_lastname'] ?? '')); ?>
                                    </div>
                                </td>
                                <td class="p-5 text-sm text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($reclamation['address']); ?>
                                </td>
                                <td class="p-5">
                                    <div
                                        class="max-w-md text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <?= nl2br(htmlspecialchars($reclamation['message'])); ?>
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

<?php
require_once __DIR__ . '/../partials/script.php';
require_once __DIR__ . '/../partials/footer.php';
?>