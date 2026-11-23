<?php require_once __DIR__ . '/../partials/head.php'; ?>
<div class="d-flex bo">
  <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
  <div class="flex-grow-1">
    <?php require_once __DIR__ . '/../partials/header.php'; ?>
    <main class="p-4">
      <?php require_once __DIR__ . '/../partials/flash.php'; ?>
      <?= $content ?>
    </main>
    <footer class="px-4 pb-3 small">© 2026 HealthConnect, administration</footer>
  </div>
</div>
<?php
require_once __DIR__ . '/../partials/modal-rejet.php';
require_once __DIR__ . '/../partials/scripts.php';
