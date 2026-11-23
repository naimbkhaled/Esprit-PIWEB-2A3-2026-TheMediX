<!-- ===== SIDEBAR ($currentPage) ===== -->
<aside class="sidebar p-3">
  <h5 class="mb-4"><i class="bi bi-heart-pulse"></i> HealthConnect</h5>
  <a href="<?= e(url('admin-groupes')) ?>" class="<?= ($currentPage ?? '') === 'admin-groupes' ? 'active' : '' ?>"><i class="bi bi-people"></i> Groupes</a>
  <a href="<?= e(url('admin-logout')) ?>" class="mt-4"><i class="bi bi-box-arrow-left"></i> Déconnexion</a>
</aside>
