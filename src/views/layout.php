<?php require_once __DIR__ . '/partials/head.php'; ?>

<body class="bg-surface transition-colors duration-300">
    <?php
    // check if you are currently in the admin folder to toggle between the admin sidebar, and the normal navbar
    $isAdminPage = strpos($_SERVER['REQUEST_URI'], '/admin') === 0 || $_SERVER['REQUEST_URI'] === '/admin';
    if ($isAdminPage && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'):
        require_once __DIR__ . '/partials/admin_sidebar.php';
        ?>
        <div class="lg:ml-64 min-h-screen">
            <main>
                <?= $content ?? '' ?>
            </main>
        </div>
    <?php else: ?>
        <?php require_once __DIR__ . '/partials/nav.php'; ?>
        <main>
            <?= $content ?? '' ?>
        </main>
        <?php require_once __DIR__ . '/partials/footer.php'; ?>
    <?php endif; ?>

    <?php require_once __DIR__ . '/partials/script.php'; ?>
</body>

</html>