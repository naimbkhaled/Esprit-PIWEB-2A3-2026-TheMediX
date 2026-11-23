<?php
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/navbar.php';
?>
<main class="container py-4">
<?php require_once __DIR__ . '/../partials/flash.php'; ?>
<?= $content ?>
</main>
<?php
require_once __DIR__ . '/../partials/footer.php';
require_once __DIR__ . '/../partials/scripts.php';
