<h1>Connexion de test</h1>
<p class="sub">Réservé au développement : disparaît quand le module est intégré à l'application</p>
<form class="card" method="post" action="<?= url('/dev/connexion') ?>" style="max-width:460px">
  <?= csrf_field() ?>
  <div><label for="nom">Nom (pour un nouveau compte)</label><input id="nom" name="nom" placeholder="Utilisateur test"></div>
  <div style="margin-top:12px"><label for="email">E-mail</label><input id="email" name="email" type="email" required list="u" placeholder="test@medix.local"></div>
  <datalist id="u"><?php foreach ($users as $x): ?><option value="<?= e($x['email']) ?>"><?= e($x['nom']) ?></option><?php endforeach; ?></datalist>
  <button class="btn" type="submit" style="margin-top:16px">Se connecter</button>
</form>
