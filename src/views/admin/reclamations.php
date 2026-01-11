<?php
$title = "Gestion Réclamations - Admin KARI";
ob_start();

use App\Repositories\Impl\ReclamationRepository;
$reclamationRepo = new ReclamationRepository();
$reclamations = $reclamationRepo->findAll();
?>

<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-400 leading-tight">
                    Gestion des Réclamations
                </h1>
                <p class="text-sm text-gray-400 mt-2 tracking-wide uppercase font-bold">Suivi des litiges et réclamations</p>
            </div>
            <a href="/admin"
                class="group flex items-center gap-3 px-6 py-3 bg-gray-800/50 backdrop-blur-sm text-gray-300 rounded-2xl hover:bg-gray-700/70 hover:text-white transition-all duration-300 font-bold shadow-lg border border-gray-700/50 hover:border-gray-600/50">
                <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                Retour Dashboard
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Total</p>
                        <p class="text-2xl font-black text-white mt-2"><?= count($reclamations) ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-900/20 flex items-center justify-center border border-amber-800/50">
                        <i class="fas fa-exclamation-triangle text-amber-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Ce Mois</p>
                        <p class="text-2xl font-black text-white mt-2">
                            <?php
                            $thisMonth = 0;
                            $currentMonth = date('m');
                            foreach ($reclamations as $reclamation) {
                                if (date('m', strtotime($reclamation['created_at'])) == $currentMonth) {
                                    $thisMonth++;
                                }
                            }
                            echo $thisMonth;
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-900/20 flex items-center justify-center border border-blue-800/50">
                        <i class="fas fa-calendar-alt text-blue-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Non Traitées</p>
                        <p class="text-2xl font-black text-orange-400 mt-2">
                            <?php
                            // Assuming 'status' field exists - adjust based on your schema
                            $pending = 0;
                            foreach ($reclamations as $reclamation) {
                                if (($reclamation['status'] ?? 'pending') == 'pending') {
                                    $pending++;
                                }
                            }
                            echo $pending;
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-900/20 flex items-center justify-center border border-orange-800/50">
                        <i class="fas fa-clock text-orange-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Résolues</p>
                        <p class="text-2xl font-black text-emerald-400 mt-2">
                            <?php
                            $resolved = 0;
                            foreach ($reclamations as $reclamation) {
                                if (($reclamation['status'] ?? 'pending') == 'resolved') {
                                    $resolved++;
                                }
                            }
                            echo $resolved;
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-900/20 flex items-center justify-center border border-emerald-800/50">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($reclamations)): ?>
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-700/50 p-16 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-gray-800/70 mb-8 border border-gray-700/50">
                    <i class="fas fa-check-double text-4xl text-emerald-400"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-4">Aucune réclamation enregistrée</h3>
                <p class="text-gray-400 font-medium max-w-md mx-auto">Tout est sous contrôle ! Aucun litige n'a été signalé pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden border border-gray-700/50 transition-all duration-300">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-900/50">
                            <tr>
                                <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-blue-400"></i>
                                        <span>Date</span>
                                    </div>
                                </th>
                                <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user text-purple-400"></i>
                                        <span>Auteur</span>
                                    </div>
                                </th>
                                <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-home text-emerald-400"></i>
                                        <span>Logement</span>
                                    </div>
                                </th>
                                <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-comment-dots text-amber-400"></i>
                                        <span>Message</span>
                                    </div>
                                </th>
                                <th class="px-8 py-5 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <div class="flex items-center justify-end gap-2">
                                        <i class="fas fa-cog text-gray-400"></i>
                                        <span>Actions</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            <?php foreach ($reclamations as $reclamation): 
                                $status = $reclamation['status'] ?? 'pending';
                                $statusColors = [
                                    'pending' => 'bg-orange-900/20 text-orange-400 border-orange-800/50',
                                    'in_progress' => 'bg-blue-900/20 text-blue-400 border-blue-800/50',
                                    'resolved' => 'bg-emerald-900/20 text-emerald-400 border-emerald-800/50'
                                ];
                                $statusClass = $statusColors[$status] ?? $statusColors['pending'];
                            ?>
                                <tr class="hover:bg-gray-800/70 transition-all duration-300 group">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-white uppercase tracking-tighter">
                                                <?= date('d M Y', strtotime($reclamation['created_at'])) ?>
                                            </span>
                                            <span class="text-[10px] text-gray-400 mt-1">
                                                <?= date('H:i', strtotime($reclamation['created_at'])) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-900/30 to-purple-800/20 flex items-center justify-center border border-purple-800/50">
                                                <i class="fas fa-user text-purple-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-white">
                                                    <?= htmlspecialchars($reclamation['firstname'] . ' ' . $reclamation['lastname']) ?>
                                                </div>
                                                <div class="text-xs text-gray-400 truncate max-w-[150px]">
                                                    <?= htmlspecialchars($reclamation['email'] ?? 'N/A') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors italic">
                                                <?= htmlspecialchars($reclamation['address']) ?>
                                            </span>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-black">
                                                    Hôte:
                                                </span>
                                                <span class="text-xs text-gray-300">
                                                    <?= htmlspecialchars($reclamation['owner_firstname'] . ' ' . $reclamation['owner_lastname']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 max-w-xs">
                                        <div class="bg-gray-800/70 p-4 rounded-xl border border-gray-700/50 hover:border-gray-600/50 transition-colors">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-bold <?= $statusClass ?> px-3 py-1 rounded-full border">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                                <button onclick="expandMessage(<?= $reclamation['id'] ?>)"
                                                        class="text-xs text-gray-400 hover:text-white transition-colors">
                                                    <i class="fas fa-expand-alt"></i>
                                                </button>
                                            </div>
                                            <p class="text-sm text-gray-300 line-clamp-2 leading-relaxed" id="message-<?= $reclamation['id'] ?>">
                                                "<?= htmlspecialchars($reclamation['message']) ?>"
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <?php if ($status !== 'resolved'): ?>
                                                <form action="/admin/reclamations/resolve" method="POST" class="inline">
                                                    <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
                                                    <button type="submit"
                                                            class="p-2.5 bg-emerald-900/20 text-emerald-400 rounded-xl hover:bg-emerald-800/50 hover:text-white transition-all duration-300 border border-emerald-800/50 hover:border-emerald-700/50 shadow-sm"
                                                            title="Marquer comme résolu">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="/admin/reclamations/notify" method="POST" class="inline">
                                                    <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
                                                    <button type="submit"
                                                            class="p-2.5 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 hover:border-blue-700/50 shadow-sm"
                                                            title="Notifier l'hôte">
                                                        <i class="fas fa-bell"></i>
                                                    </button>
                                                </form>
                                                
                                                <a href="/admin/reclamations/respond?id=<?= $reclamation['id'] ?>" 
                                                   class="p-2.5 bg-amber-900/20 text-amber-400 rounded-xl hover:bg-amber-800/50 hover:text-white transition-all duration-300 border border-amber-800/50 hover:border-amber-700/50 shadow-sm"
                                                   title="Répondre">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <form action="/admin/reclamations/delete" method="POST" class="inline"
                                                  onsubmit="return confirm('Supprimer définitivement cette réclamation ?');">
                                                <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">
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
                        </tbody>
                    </table>
                </div>
                
                <div class="px-8 py-4 border-t border-gray-700/50 bg-gray-900/30">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-400">
                            <span class="font-bold"><?= count($reclamations) ?></span> réclamation(s) au total
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-3 h-3 rounded-full bg-orange-400"></div>
                                <span class="text-gray-400">En attente</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                                <span class="text-gray-400">En cours</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                <span class="text-gray-400">Résolues</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <div class="mt-8 bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 border border-gray-700/50">
            <h3 class="text-lg font-bold text-white mb-4">Actions rapides</h3>
            <div class="flex flex-wrap gap-4">
                <a href="/admin/reclamations/export" 
                   class="px-5 py-3 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 font-bold text-sm">
                    <i class="fas fa-download mr-2"></i>Exporter toutes les réclamations
                </a>
                <button onclick="showReportModal()"
                        class="px-5 py-3 bg-purple-900/20 text-purple-400 rounded-xl hover:bg-purple-800/50 hover:text-white transition-all duration-300 border border-purple-800/50 font-bold text-sm">
                    <i class="fas fa-chart-bar mr-2"></i>Générer un rapport
                </button>
                <button onclick="markAllAsResolved()"
                        class="px-5 py-3 bg-emerald-900/20 text-emerald-400 rounded-xl hover:bg-emerald-800/50 hover:text-white transition-all duration-300 border border-emerald-800/50 font-bold text-sm">
                    <i class="fas fa-check-double mr-2"></i>Tout marquer comme résolu
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function expandMessage(id) {
    const messageElement = document.getElementById(`message-${id}`);
    if (messageElement.classList.contains('line-clamp-2')) {
        messageElement.classList.remove('line-clamp-2');
        messageElement.classList.add('line-clamp-none');
    } else {
        messageElement.classList.add('line-clamp-2');
        messageElement.classList.remove('line-clamp-none');
    }
}

function showReportModal() {
}

function markAllAsResolved() {
    if (confirm('Marquer toutes les réclamations comme résolues ? Cette action est irréversible.')) {
        // AJAX call to mark all as resolved
        fetch('/admin/reclamations/resolve-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Add keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 'e') {
        showReportModal();
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>