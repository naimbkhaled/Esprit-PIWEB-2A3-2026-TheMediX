<?php
$activePage = $activePage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'Admin – Medix', ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="assets/css/app.css">
<script src="assets/js/menu.js" defer></script>
</head>
<body class="back">
<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="brand">
            <span class="dot"></span>
            Medix Admin
        </div>
        <div class="sidebar-section-label">Main menu</div>
        <nav>
            <a href="index.php?action=admin" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <span class="icon">📊</span> Dashboard
            </a>
            <a href="index.php?action=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                <span class="icon">👤</span> My profile
            </a>
            <a href="index.php?action=dashboard">
                <span class="icon">🌐</span> Front office
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="who"><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></div>
            <div class="role-tag">Administrator</div>
            <a class="logout" href="index.php?action=logout">🚪 Log out</a>
        </div>
    </aside>
    <div class="main">
        <div class="topbar">
            <h1><?= htmlspecialchars($title ?? 'Admin Panel', ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="topbar-right">
                <span class="env-tag">BACKOFFICE</span>
                <?php require __DIR__ . '/partials/profile_menu.php'; ?>
            </div>
        </div>
        <div class="content">
            <?= $content ?>
        </div>
    </div>
</div>
</body>
</html>
