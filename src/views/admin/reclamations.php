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

    <div class="px-4 py-8 max-w-7xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white leading-tight">Gestion des Réclamations
                </h1>
                <p class="text-sm text-secondary mt-1 tracking-wide uppercase font-bold opacity-60">Suivi des litiges
                </p>
            </div>
            <a href="/admin"
                class="group flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary hover:text-white transition-all font-bold shadow-sm">
                <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                Retour Dashboard
            </a>
        </div>

        <?php if (empty($reclamations)): ?>
            <div
                class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-900 mb-6 text-gray-300 dark:text-gray-600">
                    <i class="fas fa-check-double text-3xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium text-lg">Aucune réclamation enregistrée. Tout est
                    sous contrôle !</p>
            </div>
        <?php else: ?>
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                            <tr>
                                <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                                    Date</th>
                                <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                                    Auteur</th>
                                <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                                    Logement</th>
                                <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                                    Message</th>
                                <th
                                    class="px-8 py-5 text-right text-xs font-black text-secondary uppercase tracking-widest">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 transition-colors">
                            <?php foreach ($reclamations as $reclamation): ?>
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 transition-colors group">
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-xs font-bold text-gray-400 uppercase tracking-tighter">
                                        <?= date('d M Y', strtotime($reclamation['created_at'])) ?>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($reclamation['firstname'] . ' ' . $reclamation['lastname']) ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary transition-colors italic">
                                                <?= htmlspecialchars($reclamation['address']) ?>
                                            </span>
                                            <span
                                                class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-widest font-black opacity-60">
                                                Hôte:
                                                <?= htmlspecialchars($reclamation['owner_firstname'] . ' ' . $reclamation['owner_lastname']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 max-w-xs">
                                        <div
                                            class="bg-gray-50 dark:bg-gray-900/30 p-3 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                                "<?= htmlspecialchars($reclamation['message']) ?>"
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="/admin/reclamations/notify" method="POST" class="inline"
                                                onsubmit="return confirm('Notifier l\'hôte ?');">
                                                <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
                                                <button type="submit"
                                                    class="p-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm active:scale-95"
                                                    title="Notifier l'hôte">
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            </form>
                                            <form action="/admin/reclamations/delete" method="POST" class="inline"
                                                onsubmit="return confirm('Supprimer cette réclamation ?');">
                                                <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
                                                <button type="submit"
                                                    class="p-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm active:scale-95"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>