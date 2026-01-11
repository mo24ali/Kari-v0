<?php
$current_page = $_SERVER['REQUEST_URI'];
?>

<div class="fixed left-0 top-0 h-full w-64 bg-slate-900 text-slate-300 transition-all duration-300 z-50 transform -translate-x-full lg:translate-x-0 shadow-2xl"
    id="admin-sidebar">
    <div class="p-6">
        <div class="flex items-center gap-3 mb-10">
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                <i class="fas fa-crown text-white"></i>
            </div>
            <span class="text-xl font-black text-white tracking-tight">KARI <span
                    class="text-primary">ADMIN</span></span>
        </div>

        <nav class="space-y-2">
            <a href="/admin"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $current_page === '/admin' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fas fa-th-large w-5"></i>
                <span class="font-bold text-sm">Dashboard</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Gestion</span>
            </div>

            <a href="/admin/users"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $current_page === '/admin/users' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fas fa-users w-5"></i>
                <span class="font-bold text-sm">Utilisateurs</span>
            </a>

            <a href="/admin/logements"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $current_page === '/admin/logements' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fas fa-home w-5"></i>
                <span class="font-bold text-sm">Logements</span>
            </a>

            <a href="/admin/reclamations"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $current_page === '/admin/reclamations' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fas fa-exclamation-circle w-5"></i>
                <span class="font-bold text-sm">Réclamations</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Système</span>
            </div>

            <a href="/"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all text-slate-400">
                <i class="fas fa-external-link-alt w-5"></i>
                <span class="font-bold text-sm">Voir le site</span>
            </a>
        </nav>
    </div>

    <div class="absolute bottom-0 w-full p-6 border-t border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-primary font-bold">
                <?= strtoupper(substr($_SESSION['user_firstname'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="text-xs font-bold text-white truncate">
                    <?= htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname']) ?>
                </p>
                <p class="text-[10px] text-slate-500 truncate">Administrateur</p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Toggle -->
<button
    class="fixed bottom-6 right-6 lg:hidden w-14 h-14 bg-primary text-white rounded-full shadow-2xl z-[60] flex items-center justify-center"
    id="sidebar-toggle">
    <i class="fas fa-bars text-xl"></i>
</button>

<script>
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        const sidebar = document.getElementById('admin-sidebar');
        const toggleIcon = document.querySelector('#sidebar-toggle i');
        sidebar.classList.toggle('-translate-x-full');
        toggleIcon.classList.toggle('fa-bars');
        toggleIcon.classList.toggle('fa-times');
    });
</script>