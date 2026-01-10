<?php
$title = "Gestion Utilisateurs - Admin KARI";
ob_start();

use App\Repositories\Impl\UserRepository;
$userRepo = new UserRepository();
$users = $userRepo->findAll(100); // Limit 100 for now
?>

<div class="px-4 py-8 max-w-7xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white leading-tight">Gestion des Utilisateurs</h1>
            <p class="text-sm text-secondary mt-1 tracking-wide uppercase font-bold opacity-60">Administration globale
            </p>
        </div>
        <a href="/admin"
            class="group flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary hover:text-white transition-all font-bold shadow-sm">
            <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
            Retour Dashboard
        </a>
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">
                            Utilisateur</th>
                        <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">Rôle
                        </th>
                        <th class="px-8 py-5 text-left text-xs font-black text-secondary uppercase tracking-widest">Date
                            création</th>
                        <th class="px-8 py-5 text-right text-xs font-black text-secondary uppercase tracking-widest">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 transition-colors">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 transition-colors group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-primary/10 to-blue-600/10 rounded-2xl flex items-center justify-center font-black text-primary shadow-inner border border-primary/10 transition-transform group-hover:scale-110">
                                        <?= strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary transition-colors">
                                            <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                            <i
                                                class="far fa-envelope mr-1 opacity-50"></i><?= htmlspecialchars($user['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <?php
                                $roleClass = match ($user['role']) {
                                    'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-900/50',
                                    'host' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-900/50',
                                    default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-900/50'
                                };
                                ?>
                                <span
                                    class="px-4 py-1.5 inline-flex text-[10px] leading-5 font-black rounded-full border shadow-sm uppercase tracking-wider <?= $roleClass ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-medium">
                                <i
                                    class="far fa-calendar-alt mr-2 opacity-50"></i><?= date('d M Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <form action="/admin/users/delete" method="POST" class="inline"
                                        onsubmit="return confirm('Attention : suppression irréversible. Confirmer ?');">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit"
                                            class="p-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm active:scale-90">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
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