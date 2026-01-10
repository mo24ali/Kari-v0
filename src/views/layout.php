<?php require_once __DIR__ . '/partials/head.php'; ?>

<body class="bg-surface transition-colors duration-300">
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main>
        <?= $content ?? '' ?>
    </main>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>
    <?php require_once __DIR__ . '/partials/script.php'; ?>
</body>

</html>