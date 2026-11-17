<?php
if (!isset($currentPage)) { $currentPage = ''; }
if (!function_exists('navActive')) {
    function navActive($page, $currentPage) { return $page === $currentPage ? 'active' : ''; }
}
$base = preg_replace('#/Views$#', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
$router = $base . '/index.php';
?>
<nav class="navbar">
    <a href="<?= $router ?>" class="brand">THE MEDIX</a>
    <div class="links">
        <a href="<?= $router ?>?page=home" class="<?= navActive('home', $currentPage) ?>">Accueil</a>
        <a href="<?= $router ?>?page=medicaments" class="<?= navActive('medicaments', $currentPage) ?>">Médicaments</a>
        <a href="<?= $router ?>?page=traitements" class="<?= navActive('traitements', $currentPage) ?>">Traitements</a>
        <a href="<?= $router ?>?page=allergies" class="<?= navActive('allergies', $currentPage) ?>">Allergies</a>
    </div>
    <button id="theme-toggle" type="button">Clair / Sombre</button>
</nav>