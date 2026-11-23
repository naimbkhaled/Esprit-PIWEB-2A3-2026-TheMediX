<main class="container py-5 text-center" style="max-width:720px">
  <div class="d-flex justify-content-end mb-3"><button id="themeToggle" class="btn btn-sm btn-outline-secondary" type="button"></button></div>
  <h1 class="mb-1"><i class="bi bi-heart-pulse text-hc"></i> HealthConnect</h1><p class="mb-4">Choisissez votre espace</p>
  <div class="row g-3">
    <div class="col-md-6"><a href="<?= e(url('groupes')) ?>" class="panel p-4 d-block text-decoration-none h-100"><i class="bi bi-person-heart display-4 text-hc"></i><h2 class="h5 mt-2">Espace patient</h2><p class="mb-0">Créer et rejoindre des groupes de soutien</p></a></div>
    <div class="col-md-6"><a href="<?= e(url('admin-login')) ?>" class="panel p-4 d-block text-decoration-none h-100"><i class="bi bi-shield-lock display-4 text-hc"></i><h2 class="h5 mt-2">Espace administrateur</h2><p class="mb-0">Connexion par identifiant et mot de passe</p></a></div>
  </div>
</main>
