<?php /* Titre, sous-titre et lien retour affichés dans l’en-tête de page. */ ?>

<form class="card" method="post" action="<?= url('/partage') ?>">
  <?= csrf_field() ?>
  <div class="card-header">
    <h2>Catégories de données</h2>
  </div>
  <div style="margin-bottom:1rem">
    <?php foreach ($cats as $k => $label): ?>
      <div class="check" style="padding:.75rem 0;border-bottom:1px solid var(--border)">
        <input type="checkbox" id="c_<?= e($k) ?>" name="cat[<?= e($k) ?>]" value="1" <?= !empty($etat[$k]) ? 'checked' : '' ?>>
        <label for="c_<?= e($k) ?>" style="margin:0"><?= e($label) ?></label>
      </div>
    <?php endforeach; ?>
  </div>
  <button class="btn" type="submit">Enregistrer mes préférences</button>
</form>
<p class="note">Par défaut, rien n'est partagé. Vous pouvez retirer l'accès à tout moment. L'historique des alertes de glycémie et des étapes SOS reste visible par le médecin, comme prévu dans la présentation du projet.</p>
