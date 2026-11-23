<?php /* Titre et sous-titre affichés dans l’en-tête de page. */ ?>
<div class="auth">
  <form class="card" method="post" action="<?= url('/dev/connexion') ?>">
    <?= csrf_field() ?>
    <div class="card-header">
      <h2>Connexion développeur</h2>
    </div>
    <div>
      <label for="nom">Nom (pour un nouveau compte)</label>
      <input id="nom" name="nom" placeholder="Utilisateur test">
    </div>
    <div style="margin-top:.75rem">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required list="u" placeholder="test@medix.local">
    </div>
    <datalist id="u"><?php foreach ($users as $x): ?><option value="<?= e($x['email']) ?>"><?= e($x['nom']) ?></option><?php endforeach; ?></datalist>
    <button class="btn" type="submit" style="margin-top:1rem;width:100%">Se connecter</button>
  </form>
</div>
