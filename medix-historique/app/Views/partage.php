<p><a href="<?= url('/') ?>">← Tout mon historique</a></p>
<h1>Partage avec mon médecin</h1>
<p class="sub">Choisissez ce que votre médecin peut voir</p>

<form class="card" method="post" action="<?= url('/partage') ?>">
  <?= csrf_field() ?>
  <?php foreach ($cats as $k => $label): ?>
    <div class="check" style="padding:8px 0">
      <input type="checkbox" id="c_<?= e($k) ?>" name="cat[<?= e($k) ?>]" value="1" <?= !empty($etat[$k]) ? 'checked' : '' ?>>
      <label for="c_<?= e($k) ?>" style="margin:0"><?= e($label) ?></label>
    </div>
  <?php endforeach; ?>
  <button class="btn" type="submit" style="margin-top:14px">Enregistrer</button>
</form>
<p class="note">Par défaut, rien n'est partagé. Vous pouvez retirer l'accès à tout moment. L'historique des alertes de glycémie et des étapes SOS reste visible par le médecin, comme prévu dans la présentation du projet.</p>
