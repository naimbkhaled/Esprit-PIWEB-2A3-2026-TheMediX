<?php $qs = ['q' => $q, 'statut' => $statut]; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Gestion des groupes</h1>
  <a class="btn btn-outline-success" href="<?= e(url('admin-export', $qs)) ?>"><i class="bi bi-file-earmark-excel"></i> Exporter en Excel</a>
</div>
<form class="row g-2 mb-3" method="get" action="index.php">
  <input type="hidden" name="page" value="admin-groupes">
  <div class="col-md-6"><input type="text" name="q" class="form-control" placeholder="Rechercher un groupe" value="<?= e($q) ?>"></div>
  <div class="col-md-4"><select name="statut" class="form-select"><?= statut_options($statut) ?></select></div>
  <div class="col-md-2 d-grid"><button class="btn btn-hc">Filtrer</button></div>
</form>
<div class="panel p-3 mb-3"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th>#</th><th>Nom</th><th>Créateur</th><th>Statut</th><th>Membres</th><th>Créé le</th><th>Actions</th></tr></thead>
  <tbody>
  <?php if (!$groupes): ?><tr><td colspan="7">Aucun groupe trouvé.</td></tr><?php endif; ?>
  <?php foreach ($groupes as $g): ?>
    <tr><td><?= $g->getId() ?></td><td><?= e($g->getNom()) ?></td><td><?= e($g->getNomCreateur()) ?></td><td><?= statut_badge($g->getStatut()) ?></td><td><?= $g->getNbMembres() ?></td><td><?= e(substr($g->getDateCreation(), 0, 10)) ?></td>
    <td class="d-flex flex-wrap gap-1">
      <a href="<?= e(url('admin-groupe-detail', ['id' => $g->getId()])) ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
      <?php if ($g->getStatut() === 'en_attente'): ?>
        <form method="post" action="<?= e(url('admin-approve')) ?>" onsubmit="return confirm('Approuver ce groupe ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $g->getId() ?>"><button class="btn btn-sm btn-success">Approuver</button></form>
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejet" onclick="document.getElementById('rejet-id').value='<?= $g->getId() ?>'">Rejeter</button>
      <?php endif; ?>
    </td></tr>
  <?php endforeach; ?>
  </tbody></table></div></div>
<?= pagination($page, $pages, 'admin-groupes', $qs) ?>
