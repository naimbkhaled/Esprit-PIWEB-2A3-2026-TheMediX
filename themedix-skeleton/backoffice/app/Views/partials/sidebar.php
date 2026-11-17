<?php
if (!isset($currentPage)) { $currentPage = ''; }
if (!function_exists('navActive')) {
    function navActive($page, $currentPage) { return $page === $currentPage ? 'active' : ''; }
}
?>
<aside class="sidebar" id="sidebar">
    <div class="logo">THE MEDIX <small>Admin</small></div>
    <nav>
        <a href="index.php?page=dashboard" class="<?= navActive('dashboard', $currentPage) ?>">📊 Dashboard</a>
        <a href="index.php?page=medicaments" class="<?= navActive('medicaments', $currentPage) ?>">💊 Médicaments</a>
        <a href="index.php?page=traitements" class="<?= navActive('traitements', $currentPage) ?>">📋 Traitements</a>
        <a href="index.php?page=allergies" class="<?= navActive('allergies', $currentPage) ?>">⚠️ Allergies</a>
    </nav>
</aside>