<nav class="sticky top-0 z-50 glass-effect border-b border-light">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-home text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold tracking-tight">KARI</span>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-sm font-medium hover:text-primary transition-colors">Explorer</a>
                <a href="/reservations"
                    class="text-sm font-medium hover:text-primary transition-colors">Réservations</a>
                <a href="/favoris" class="text-sm font-medium hover:text-primary transition-colors">Favoris</a>
                <a href="/hote" class="text-sm font-medium hover:text-primary transition-colors">Hôte</a>
            </div>

            <div class="flex items-center space-x-4">
                <button id="theme-toggle" class="p-2 rounded-lg hover:bg-surface transition-colors">
                    <i class="fas fa-moon text-lg"></i>
                </button>

                </a>

                <?php
                // Fetch unread notifications
                $unreadCount = 0;
                if ($authService->isAuth()) {
                    $notifRepo = new \App\Repositories\Impl\NotificationRepository();
                    $notifService = new \App\Services\NotificationService($notifRepo);
                    $unreadCount = $notifService->getUnreadCount($authService->getUserId());
                }
                ?>
                <div class="relative">
                    <button id="notif-btn" class="p-2 rounded-lg hover:bg-surface transition-colors">
                        <i class="fas fa-bell text-lg <?php echo $unreadCount > 0 ? 'text-primary' : ''; ?>"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span
                                class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold text-white">
                                <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <div id="notif-dropdown"
                        class="absolute right-0 mt-2 w-80 bg-surface rounded-lg shadow-custom-lg border border-light hidden z-50">
                        <div class="p-4 border-b border-light font-bold flex justify-between items-center">
                            <span>Notifications</span>
                            <?php if ($unreadCount > 0): ?>
                                <button onclick="markAllAsRead()" class="text-xs text-primary font-normal">Tout marquer
                                    lu</button>
                            <?php endif; ?>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <?php
                            if ($authService->isAuth()) {
                                $notifications = $notifService->getUserNotifications($authService->getUserId());
                                if (empty($notifications)) {
                                    echo '<div class="p-8 text-center text-secondary italic">Aucune notification</div>';
                                } else {
                                    foreach ($notifications as $n) {
                                        $bgClass = $n->isRead() ? '' : 'bg-blue-50 dark:bg-blue-900/10';
                                        echo "<div class='p-4 border-b border-light hover:bg-surface-secondary transition-colors $bgClass'>
                                                <div class='text-sm font-medium'>" . htmlspecialchars($n->getMessage()) . "</div>
                                                <div class='text-[10px] text-secondary mt-1'>" . $n->getCreatedAt() . "</div>
                                              </div>";
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <?php
                $userName = $_SESSION['user_name'] ?? 'Utilisateur';
                $userEmail = $_SESSION['user_email'] ?? '';
                $userFirstname = $_SESSION['user_firstname'] ?? '';
                $userLastname = $_SESSION['user_lastname'] ?? '';

                $initials = '';
                if ($userFirstname && $userLastname) {
                    $initials = strtoupper(substr($userFirstname, 0, 1) . substr($userLastname, 0, 1));
                } elseif ($userName && $userName !== 'Utilisateur') {
                    $parts = explode(' ', $userName);
                    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                } else {
                    $initials = 'U';
                }
                ?>
                <div class="relative">
                    <button id="user-menu-btn"
                        class="flex items-center space-x-2 p-1 rounded-lg hover:bg-surface transition-colors">
                        <a href="/profile"
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-medium hover:opacity-80 transition-opacity">
                            <?php echo htmlspecialchars($initials); ?>
                        </a>
                        <i class="fas fa-chevron-down text-xs text-secondary"></i>
                    </button>

                    <div id="user-menu"
                        class="absolute right-0 mt-2 w-56 bg-surface rounded-lg shadow-custom-lg border border-light hidden">
                        <div class="p-4 border-b border-light">
                            <div class="font-medium"><?php echo htmlspecialchars($userName); ?></div>
                            <div class="text-sm text-secondary"><?php echo htmlspecialchars($userEmail); ?></div>
                        </div>
                        <div class="p-2">
                            <a href="/profile"
                                class="flex items-center px-3 py-2 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <i class="fas fa-user mr-3 text-secondary"></i>
                                <span>Profil</span>
                            </a>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <a href="/admin"
                                    class="flex items-center px-3 py-2 rounded hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors">
                                    <i class="fas fa-shield-alt mr-3 text-purple-600 dark:text-purple-400"></i>
                                    <span class="text-purple-700 dark:text-purple-300 font-medium">Administration</span>
                                </a>
                            <?php endif; ?>
                            <a href="#"
                                class="flex items-center px-3 py-2 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <i class="fas fa-home mr-3 text-secondary"></i>
                                <span>Mes logements</span>
                            </a>
                            <a href="/favoris"
                                class="flex items-center px-3 py-2 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <i class="fas fa-heart mr-3 text-secondary"></i>
                                <span>Favoris</span>
                            </a>
                            <a href="/reservations"
                                class="flex items-center px-3 py-2 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <i class="fas fa-receipt mr-3 text-secondary"></i>
                                <span>Réservations</span>
                            </a>
                            <div class="border-t border-light my-2"></div>
                            <a href="/logout"
                                class="flex items-center px-3 py-2 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                <span>Déconnexion</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>