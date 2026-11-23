<h1 class="h3 mb-3"><?= $id ? 'Modifier le groupe' : 'Créer un groupe' ?></h1>
<form action="<?= e(url('groupe-save')) ?>" method="post" class="panel p-4" onsubmit="return validerGroupe()" novalidate>
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
  <?= champ('nom', 'Nom du groupe', 'input', $old, $errors) ?>
  <?= champ('description', 'Description', 'area', $old, $errors) ?>
  <?= champ('regles', 'Règles du groupe', 'area', $old, $errors) ?>
  <p class="small">Un nouveau groupe est « en attente » jusqu’à l’approbation de l’administrateur.</p>
  <button class="btn btn-hc">Enregistrer</button> <a href="<?= e(url('groupes')) ?>" class="btn btn-outline-secondary">Annuler</a>
</form>
