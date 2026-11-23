<a href="<?= e(url('admin-groupes')) ?>" class="text-hc text-decoration-none">&larr; Retour à la liste</a>
<div class="panel p-4 my-3 d-flex flex-wrap justify-content-between">
  <div><h1 class="h4"><?= e($groupe->getNom()) ?></h1><p class="mb-1"><?= e($groupe->getDescription()) ?></p><?= statut_badge($groupe->getStatut()) ?> <small>Créé par <?= e($groupe->getNomCreateur()) ?> le <?= e(substr($groupe->getDateCreation(), 0, 10)) ?></small>
    <?php if ($groupe->getStatut() === 'rejete'): ?><p class="text-danger mb-0 mt-2">Motif du rejet : <?= e($groupe->getMotifRejet()) ?></p><?php endif; ?></div>
  <?php if ($groupe->getStatut() === 'en_attente'): ?>
  <div class="d-flex gap-2 align-self-start">
    <form method="post" action="<?= e(url('admin-approve')) ?>" onsubmit="return confirm('Approuver ce groupe ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $groupe->getId() ?>"><button class="btn btn-success">Approuver</button></form>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejet" onclick="document.getElementById('rejet-id').value='<?= $groupe->getId() ?>'">Rejeter</button>
  </div>
  <?php endif; ?>
</div>
<div class="row g-3">
  <div class="col-lg-7"><div class="panel p-3 h-100"><h2 class="h6">Règles du groupe</h2><p class="mb-0" style="white-space:pre-line"><?= e($groupe->getRegles()) ?></p></div></div>
  <div class="col-lg-5"><div class="panel p-3"><h2 class="h6">Membres (<?= count($membres) ?>)</h2><ul class="mb-0"><?php foreach ($membres as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div></div>
</div>
