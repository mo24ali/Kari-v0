<?php
$title = "Gestion Logements - Admin KARI";
ob_start();

use App\Repositories\Impl\LogementRepository;
$logementRepo = new LogementRepository();
$logements = $logementRepo->findAll();
?>

<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400 leading-tight">
                    Gestion des Logements
                </h1>
                <p class="text-sm text-gray-400 mt-2 tracking-wide uppercase font-bold">Catalogue des propriétés</p>
            </div>
            <a href="/admin"
                class="group flex items-center gap-3 px-6 py-3 bg-gray-800/50 backdrop-blur-sm text-gray-300 rounded-2xl hover:bg-gray-700/70 hover:text-white transition-all duration-300 font-bold shadow-lg border border-gray-700/50 hover:border-gray-600/50">
                <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                Retour Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-8 px-6 py-4 bg-emerald-900/20 border border-emerald-800/50 text-emerald-300 rounded-2xl flex items-center gap-3 animate-in zoom-in duration-300 backdrop-blur-sm">
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-check text-emerald-400 text-sm"></i>
                </div>
                <span class="font-bold"><?= htmlspecialchars($_SESSION['success']) ?></span>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden border border-gray-700/50 transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-home text-blue-400"></i>
                                    <span>Logement</span>
                                </div>
                            </th>
                            <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-emerald-400"></i>
                                    <span>Hôte</span>
                                </div>
                            </th>
                            <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-euro-sign text-amber-400"></i>
                                    <span>Prix/Nuit</span>
                                </div>
                            </th>
                            <th class="px-8 py-5 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                <div class="flex items-center justify-end gap-2">
                                    <i class="fas fa-cog text-purple-400"></i>
                                    <span>Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        <?php if (empty($logements)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3 text-gray-400">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-800/50 flex items-center justify-center border border-gray-700/50">
                                            <i class="fas fa-home text-2xl"></i>
                                        </div>
                                        <p class="font-bold">Aucun logement trouvé</p>
                                        <p class="text-sm">Commencez par ajouter un logement</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logements as $log): ?>
                                <tr class="hover:bg-gray-800/70 transition-all duration-300 group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-14 w-14 group-hover:scale-105 transition-transform duration-300">
                                                <?php if ($log->getPrimaryImage()): ?>
                                                    <img class="h-14 w-14 rounded-2xl object-cover shadow-lg border border-gray-700/50 group-hover:border-blue-500/50 transition-colors"
                                                        src="<?= htmlspecialchars($log->getPrimaryImage()) ?>" 
                                                        alt="Logement <?= $log->getId() ?>">
                                                <?php else: ?>
                                                    <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center border border-gray-700/50 group-hover:border-blue-500/50 transition-colors">
                                                        <i class="fas fa-home text-gray-400 group-hover:text-blue-400 transition-colors"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">
                                                    <?= htmlspecialchars($log->getAddress() ?? 'Sans adresse') ?>
                                                </div>
                                                <div class="text-[10px] font-black tracking-widest text-gray-500 uppercase">
                                                    ID #<?= $log->getId() ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-900/30 to-emerald-800/20 flex items-center justify-center border border-emerald-800/50">
                                                <i class="fas fa-user text-emerald-400 text-sm"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-white">
                                                    <?php
                                                    $owner = $log->getOwner();
                                                    echo htmlspecialchars(($owner['firstname'] ?? 'Inconnu') . ' ' . ($owner['lastname'] ?? ''));
                                                    ?>
                                                </span>
                                                <span class="text-xs text-gray-400 truncate max-w-[200px]">
                                                    <?= htmlspecialchars($owner['email'] ?? '') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-900/30 to-amber-800/20 flex items-center justify-center border border-amber-800/50">
                                                <i class="fas fa-euro-sign text-amber-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-black text-amber-400">
                                                    <?= number_format($log->getPrice(), 0) ?> €
                                                </span>
                                                <span class="text-[10px] font-normal text-gray-400 block">/ nuit</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="/logement/details?id=<?= $log->getId() ?>" 
                                               class="p-2.5 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 hover:border-blue-700/50 shadow-sm"
                                               title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/logements/edit?id=<?= $log->getId() ?>" 
                                               class="p-2.5 bg-emerald-900/20 text-emerald-400 rounded-xl hover:bg-emerald-800/50 hover:text-white transition-all duration-300 border border-emerald-800/50 hover:border-emerald-700/50 shadow-sm"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/admin/logements/delete" method="POST" class="inline"
                                                onsubmit="return confirm('Attention : suppression irréversible. Confirmer ?');">
                                                <input type="hidden" name="id" value="<?= $log->getId() ?>">
                                                <button type="submit"
                                                    class="p-2.5 bg-red-900/20 text-red-400 rounded-xl hover:bg-red-800/50 hover:text-white transition-all duration-300 border border-red-800/50 hover:border-red-700/50 shadow-sm active:scale-95"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($logements)): ?>
                <div class="px-8 py-4 border-t border-gray-700/50 bg-gray-900/30">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-400">
                            <span class="font-bold"><?= count($logements) ?></span> logement(s) total
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-gray-400">Actions groupées :</span>
                            <button onclick="exportLogements()" 
                                    class="px-4 py-2 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 text-sm font-bold">
                                <i class="fas fa-download mr-2"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Stats Summary -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Prix Moyen</p>
                        <p class="text-2xl font-black text-white mt-2">
                            <?php 
                            $total = 0;
                            $count = 0;
                            foreach ($logements as $log) {
                                $total += $log->getPrice();
                                $count++;
                            }
                            echo $count > 0 ? number_format($total / $count, 0) : '0';
                            ?> €
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-900/20 flex items-center justify-center border border-blue-800/50">
                        <i class="fas fa-chart-line text-blue-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Logements Actifs</p>
                        <p class="text-2xl font-black text-white mt-2"><?= count($logements) ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-900/20 flex items-center justify-center border border-emerald-800/50">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Hôtes Uniques</p>
                        <p class="text-2xl font-black text-white mt-2">
                            <?php
                            $hosts = [];
                            foreach ($logements as $log) {
                                $owner = $log->getOwner();
                                if (isset($owner['id'])) {
                                    $hosts[$owner['id']] = true;
                                }
                            }
                            echo count($hosts);
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-900/20 flex items-center justify-center border border-purple-800/50">
                        <i class="fas fa-users text-purple-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>