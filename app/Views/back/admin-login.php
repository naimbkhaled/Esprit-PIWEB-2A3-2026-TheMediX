<main class="container py-5" style="max-width:440px">
  <div class="panel p-4">
    <h1 class="h4 mb-3"><i class="bi bi-shield-lock text-hc"></i> Connexion administrateur</h1>
    <form action="<?= e(url('admin-login')) ?>" method="post" onsubmit="return validerLogin()" novalidate>
      <?= csrf_field() ?>
      <?= champ('login', 'Identifiant', 'input', $old, $errors) ?>
      <?= champ('mdp', 'Mot de passe', 'password', [], $errors) ?>
      <button class="btn btn-hc w-100">Se connecter</button>
    </form>
    <div class="d-flex justify-content-between mt-3"><button id="themeToggle" class="btn btn-sm btn-outline-secondary" type="button"></button><a href="<?= e(url('home')) ?>" class="small">&larr; Retour</a></div>
  </div>
</main>
