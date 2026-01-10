<?php
$title = "Gestion Logements - Admin KARI";
ob_start();

use App\Repositories\Impl\LogementRepository;
$logementRepo = new LogementRepository();
$logements = $logementRepo->findAll();
?>

<div class="px-4 py-8 max-w-7xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white leading-tight">Gestion des Logements</h1>
            <p class="text-sm text-secondary mt-1 tracking-wide uppercase font-bold opacity-60">Catalogue des propriétés
            </p>
        </div>
        <a href="/admin"
            class="group flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary hover:text-white transition-all font-bold shadow-sm">
            <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
            Retour Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div
            class="mb-8 px-6 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900/30 text-green-700 dark:text-green-400 rounded-2xl flex items-center gap-3 animate-in zoom-in duration-300">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold"><?= htmlspecialchars($_SESSION['success']) ?></span>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                            Logement</th>
                        <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">Hôte
                        </th>
                        <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                            Prix/Nuit</th>
                        <th class="px-8 py-5 text-right text-xs font-black text-secondary uppercase tracking-widest">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 transition-colors">
                    <?php foreach ($logements as $log): ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 transition-colors group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-14 w-14 group-hover:scale-105 transition-transform duration-300">
                                        <?php if ($log->getPrimaryImage()): ?>
                                            <img class="h-14 w-14 rounded-2xl object-cover shadow-md border border-gray-100 dark:border-gray-700"
                                                src="<?= htmlspecialchars($log->getPrimaryImage()) ?>" alt="">
                                        <?php else: ?>
                                            <div
                                                class="h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                                <i class="fas fa-home text-xl"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary transition-colors">
                                            <?= htmlspecialchars($log->getAddress() ?? 'Sans adresse') ?>
                                        </div>
                                        <div
                                            class="text-[10px] font-black tracking-widest text-secondary uppercase opacity-60">
                                            ID #<?= $log->getId() ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        <?php
                                        $owner = $log->getOwner();
                                        echo htmlspecialchars(($owner['firstname'] ?? 'Inconnu') . ' ' . ($owner['lastname'] ?? ''));
                                        ?>
                                    </span>
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($owner['email'] ?? '') ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-sm font-black text-primary">
                                    <?= number_format($log->getPrice(), 0) ?> €
                                    <span class="text-[10px] font-normal text-secondary">/ nuit</span>
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                <form action="/admin/logements/delete" method="POST" class="inline"
                                    onsubmit="return confirm('Attention : suppression irréversible. Confirmer ?');">
                                    <input type="hidden" name="id" value="<?= $log->getId() ?>">
                                    <button type="submit"
                                        class="p-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm active:scale-90">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>