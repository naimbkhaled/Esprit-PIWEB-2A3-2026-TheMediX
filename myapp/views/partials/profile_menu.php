<?php
$menuName = (string) ($_SESSION['username'] ?? '');
$menuRole = (string) ($_SESSION['role'] ?? 'user');
?>
<details class="profile-menu">
    <summary class="profile-trigger" aria-label="Profile menu">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"></path>
        </svg>
    </summary>
    <div class="profile-dropdown">
        <div class="profile-head">
            <strong><?= htmlspecialchars($menuName, ENT_QUOTES, 'UTF-8') ?></strong>
            <span><?= htmlspecialchars($menuRole, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <a href="index.php?action=profile">Consult my information</a>
        <a href="index.php?action=profile_edit">Edit my information</a>
        <a href="index.php?action=logout" class="danger">Log out</a>
    </div>
</details>
