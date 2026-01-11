<?php
$title = "Gestion Utilisateurs - Admin KARI";
ob_start();

use App\Repositories\Impl\UserRepository;
$userRepo = new UserRepository();
$users = $userRepo->findAll(100); 
?>

<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 leading-tight">
                    Gestion des Utilisateurs
                </h1>
                <p class="text-sm text-gray-400 mt-2 tracking-wide uppercase font-bold">Administration globale des membres</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="/admin"
                    class="group flex items-center gap-3 px-6 py-3 bg-gray-800/50 backdrop-blur-sm text-gray-300 rounded-2xl hover:bg-gray-700/70 hover:text-white transition-all duration-300 font-bold shadow-lg border border-gray-700/50 hover:border-gray-600/50">
                    <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                    Retour Dashboard
                </a>
                <button onclick="showAddUserModal()"
                    class="group flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-bold shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30">
                    <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
                    Ajouter Utilisateur
                </button>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $totalUsers = count($users);
            $admins = array_filter($users, fn($u) => $u['role'] === 'admin');
            $hosts = array_filter($users, fn($u) => $u['role'] === 'host');
            $travellers = array_filter($users, fn($u) => $u['role'] === 'traveller');
            $todayUsers = array_filter($users, fn($u) => date('Y-m-d') === date('Y-m-d', strtotime($u['created_at'])));
            ?>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Total</p>
                        <p class="text-2xl font-black text-white mt-2"><?= $totalUsers ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-900/20 flex items-center justify-center border border-blue-800/50">
                        <i class="fas fa-users text-blue-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Administrateurs</p>
                        <p class="text-2xl font-black text-purple-400 mt-2"><?= count($admins) ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-900/20 flex items-center justify-center border border-purple-800/50">
                        <i class="fas fa-crown text-purple-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Hôtes</p>
                        <p class="text-2xl font-black text-emerald-400 mt-2"><?= count($hosts) ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-900/20 flex items-center justify-center border border-emerald-800/50">
                        <i class="fas fa-home text-emerald-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase">Aujourd'hui</p>
                        <p class="text-2xl font-black text-amber-400 mt-2"><?= count($todayUsers) ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-900/20 flex items-center justify-center border border-amber-800/50">
                        <i class="fas fa-calendar-day text-amber-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6 bg-gray-800/50 backdrop-blur-sm rounded-2xl p-4 border border-gray-700/50">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               placeholder="Rechercher un utilisateur par nom, email..."
                               class="w-full pl-12 pr-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               onkeyup="filterUsers(this.value)">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="filterByRole(this.value)">
                        <option value="">Tous les rôles</option>
                        <option value="admin">Administrateurs</option>
                        <option value="host">Hôtes</option>
                        <option value="traveller">Voyageurs</option>
                    </select>
                    <button onclick="exportUsers()"
                            class="px-5 py-3 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 font-bold">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden border border-gray-700/50 transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-circle text-purple-400"></i>
                                    <span>Utilisateur</span>
                                </div>
                            </th>
                            <th class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tag text-blue-400"></i>
                                    <span>Rôle</span>
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
                    <tbody class="divide-y divide-gray-700/50" id="usersTable">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3 text-gray-400">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-800/50 flex items-center justify-center border border-gray-700/50">
                                            <i class="fas fa-users text-2xl"></i>
                                        </div>
                                        <p class="font-bold">Aucun utilisateur trouvé</p>
                                        <p class="text-sm">Commencez par ajouter un utilisateur</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-800/70 transition-all duration-300 group" 
                                    data-role="<?= $user['role'] ?>"
                                    data-name="<?= htmlspecialchars(strtolower($user['firstname'] . ' ' . $user['lastname'])) ?>"
                                    data-email="<?= htmlspecialchars(strtolower($user['email'])) ?>">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-2xl flex items-center justify-center font-black text-purple-400 shadow-lg border border-purple-500/20 group-hover:scale-110 transition-transform group-hover:border-purple-400/50">
                                                <?= strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)) ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-white group-hover:text-purple-400 transition-colors">
                                                    <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                                                </div>
                                                <div class="text-xs text-gray-400 font-medium flex items-center gap-2">
                                                    <i class="far fa-envelope opacity-50"></i>
                                                    <span class="truncate max-w-[200px]"><?= htmlspecialchars($user['email']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <?php
                                            $roleConfig = match ($user['role']) {
                                                'admin' => [
                                                    'bg' => 'bg-purple-900/30',
                                                    'text' => 'text-purple-300',
                                                    'border' => 'border-purple-800/50',
                                                    'icon' => 'crown',
                                                    'iconColor' => 'text-purple-400'
                                                ],
                                                'host' => [
                                                    'bg' => 'bg-emerald-900/30',
                                                    'text' => 'text-emerald-300',
                                                    'border' => 'border-emerald-800/50',
                                                    'icon' => 'home',
                                                    'iconColor' => 'text-emerald-400'
                                                ],
                                                default => [
                                                    'bg' => 'bg-blue-900/30',
                                                    'text' => 'text-blue-300',
                                                    'border' => 'border-blue-800/50',
                                                    'icon' => 'user',
                                                    'iconColor' => 'text-blue-400'
                                                ]
                                            };
                                            ?>
                                            <div class="w-10 h-10 rounded-xl <?= $roleConfig['bg'] ?> flex items-center justify-center border <?= $roleConfig['border'] ?>">
                                                <i class="fas fa-<?= $roleConfig['icon'] ?> <?= $roleConfig['iconColor'] ?>"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black <?= $roleConfig['text'] ?> uppercase tracking-wider">
                                                    <?= htmlspecialchars(ucfirst($user['role'])) ?>
                                                </span>
                                                <span class="text-[10px] text-gray-400">
                                                    <?php
                                                    switch ($user['role']) {
                                                        case 'admin': echo 'Administrateur complet'; break;
                                                        case 'host': echo 'Propriétaire de logements'; break;
                                                        default: echo 'Voyageur';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="/admin/users/view/<?= $user['id'] ?>" 
                                               class="p-2.5 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 hover:border-blue-700/50 shadow-sm"
                                               title="Voir profil">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="/admin/users/edit/<?= $user['id'] ?>" 
                                               class="p-2.5 bg-emerald-900/20 text-emerald-400 rounded-xl hover:bg-emerald-800/50 hover:text-white transition-all duration-300 border border-emerald-800/50 hover:border-emerald-700/50 shadow-sm"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <?php if ($user['role'] !== 'admin'): ?>
                                                <form action="/admin/users/promote" method="POST" class="inline">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <!-- <button type="submit"
                                                            class="p-2.5 bg-purple-900/20 text-purple-400 rounded-xl hover:bg-purple-800/50 hover:text-white transition-all duration-300 border border-purple-800/50 hover:border-purple-700/50 shadow-sm"
                                                            title="Promouvoir admin"
                                                            onclick="return confirm('Promouvoir cet utilisateur en administrateur ?')">
                                                        <i class="fas fa-user-plus"></i>
                                                    </button> -->
                                                </form>
                                                
                                                <form action="/admin/users/delete" method="POST" class="inline">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit"
                                                            class="p-2.5 bg-red-900/20 text-red-400 rounded-xl hover:bg-red-800/50 hover:text-white transition-all duration-300 border border-red-800/50 hover:border-red-700/50 shadow-sm active:scale-95"
                                                            title="Supprimer"
                                                            onclick="return confirm('Suppression irréversible. Confirmer ?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="px-3 py-1 text-xs font-bold text-gray-400 bg-gray-900/30 rounded-lg border border-gray-700/50">
                                                    Admin
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($users)): ?>
                <div class="px-8 py-4 border-t border-gray-700/50 bg-gray-900/30">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-400">
                            <span class="font-bold"><?= count($users) ?></span> utilisateur(s) total
                            <span class="mx-2">•</span>
                            <span class="font-bold text-purple-400"><?= count($admins) ?></span> admin(s)
                            <span class="mx-2">•</span>
                            <span class="font-bold text-emerald-400"><?= count($hosts) ?></span> hôte(s)
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-3 h-3 rounded-full bg-purple-400"></div>
                                <span class="text-gray-400">Admin</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                <span class="text-gray-400">Hôte</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                                <span class="text-gray-400">Voyageur</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Bulk Actions -->
        <div class="mt-8 bg-gray-800/50 backdrop-blur-sm rounded-3xl p-6 border border-gray-700/50">
            <h3 class="text-lg font-bold text-white mb-4">Actions groupées</h3>
            <div class="flex flex-wrap gap-4">
                <button onclick="exportUsers()"
                        class="px-5 py-3 bg-blue-900/20 text-blue-400 rounded-xl hover:bg-blue-800/50 hover:text-white transition-all duration-300 border border-blue-800/50 font-bold text-sm">
                    <i class="fas fa-download mr-2"></i>Exporter tous les utilisateurs
                </button>
                <button onclick="sendBulkEmail()"
                        class="px-5 py-3 bg-amber-900/20 text-amber-400 rounded-xl hover:bg-amber-800/50 hover:text-white transition-all duration-300 border border-amber-800/50 font-bold text-sm">
                    <i class="fas fa-envelope mr-2"></i>Envoyer email à tous
                </button>
                <button onclick="generateReport()"
                        class="px-5 py-3 bg-purple-900/20 text-purple-400 rounded-xl hover:bg-purple-800/50 hover:text-white transition-all duration-300 border border-purple-800/50 font-bold text-sm">
                    <i class="fas fa-chart-bar mr-2"></i>Générer rapport statistique
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function filterUsers(searchTerm) {
    const rows = document.querySelectorAll('#usersTable tr[data-name]');
    const term = searchTerm.toLowerCase();
    
    //do research based on the email and name
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.getAttribute('data-email');
        
        if (name.includes(term) || email.includes(term)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterByRole(role) {
    const rows = document.querySelectorAll('#usersTable tr[data-role]');
    
    rows.forEach(row => {
        const userRole = row.getAttribute('data-role');
        
        if (!role || userRole === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>