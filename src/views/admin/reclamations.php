<?php
$title = "Gestion Réclamations - Admin KARI";
ob_start();

use App\Repositories\Impl\ReclamationRepository;
$reclamationRepo = new ReclamationRepository();
$reclamations = $reclamationRepo->findAll();
?>

<div class="px-4 py-8 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Gestion des Réclamations</h1>
        <a href="/admin" class="text-gray-600 hover:text-gray-900">Retour Dashboard</a>
    </div>

    <?php if (empty($reclamations)): ?>
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <p class="text-gray-500">Aucune réclamation enregistrée.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auteur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($reclamations as $reclamation): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($reclamation['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($reclamation['firstname'] . ' ' . $reclamation['lastname']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($reclamation['address']) ?></div>
                                <div class="text-xs text-gray-500">Hôte:
                                    <?= htmlspecialchars($reclamation['owner_firstname'] . ' ' . $reclamation['owner_lastname']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900 line-clamp-2"><?= htmlspecialchars($reclamation['message']) ?>
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="/admin/reclamations/notify" method="POST" class="inline"
                                    onsubmit="return confirm('Notifier l\'hôte ?');">
                                    <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 mr-2">Notifier Hôte</button>
                                </form>
                                <form action="/admin/reclamations/delete" method="POST" class="inline"
                                    onsubmit="return confirm('Confirmer la suppression ?');">
                                    <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                            </td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>