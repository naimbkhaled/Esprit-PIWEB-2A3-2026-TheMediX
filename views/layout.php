<?php
$isLogged = !empty($_SESSION['user_id']);
$isAdmin  = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'Medix', ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="assets/css/app.css">
<script src="assets/js/menu.js" defer></script>
</head>
<body class="front">
<nav class="topnav">
    <a class="brand" href="index.php?action=home">
        <div class="brand-icon">🩺</div>
        Medix
    </a>
    <div class="links">
        <?php if ($isLogged): ?>
            <a href="index.php?action=<?= $isAdmin ? 'admin' : 'dashboard' ?>">
                <?= $isAdmin ? '⚙️ Admin panel' : '📋 My space' ?>
            </a>
            <?php require __DIR__ . '/partials/profile_menu.php'; ?>
        <?php else: ?>
            <a href="index.php?action=login">Log in</a>
            <a href="index.php?action=register" class="btn-primary">Get started free</a>
        <?php endif; ?>
    </div>
</nav>
<?= $content ?>
</body>
</html>
