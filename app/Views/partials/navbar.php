<?php $nav = ['groupes' => 'Groupes', 'groupe-form' => 'Créer un groupe']; ?>
<!-- ===== NAVBAR ($currentPage) ===== -->
<nav class="navbar navbar-expand-lg top sticky-top"><div class="container">
  <a class="navbar-brand fw-bold" href="<?= e(url('groupes')) ?>"><i class="bi bi-heart-pulse"></i> HealthConnect</a>
  <ul class="navbar-nav flex-row gap-3 me-auto ms-4">
    <?php foreach ($nav as $k => $l): ?><li class="nav-item"><a class="nav-link<?= ($currentPage ?? '') === $k ? ' active' : '' ?>" href="<?= e(url($k)) ?>"><?= $l ?></a></li><?php endforeach; ?>
  </ul>
  <span class="me-3 small"><i class="bi bi-person-circle"></i> Espace patient</span>
  <button id="themeToggle" class="btn btn-sm btn-outline-light" type="button"></button>
  <a class="ms-3 small" href="<?= e(url('home')) ?>">Changer d’espace</a>
</div></nav>
