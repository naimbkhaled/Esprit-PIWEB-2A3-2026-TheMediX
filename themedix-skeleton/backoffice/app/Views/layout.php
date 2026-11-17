<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<div class="layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <div class="main">
        <?php require_once __DIR__ . '/includes/header.php'; ?>
        <main class="content">
            <?= $content ?>
        </main>
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>