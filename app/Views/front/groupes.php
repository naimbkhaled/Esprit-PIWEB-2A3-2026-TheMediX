<?php $uid = (int)$_SESSION['patient_id']; $qs = ['q' => $q, 'statut' => $statut, 'onglet' => $mine ? 'mes' : '']; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Groupes de soutien</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-success" href="<?= e(url('groupes-export', $qs)) ?>"><i class="bi bi-file-earmark-excel"></i> Exporter en Excel</a>
    <a class="btn btn-hc" href="<?= e(url('groupe-form')) ?>"><i class="bi bi-plus-lg"></i> Créer un groupe</a>
  </div>
</div>
<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><a class="nav-link<?= $mine ? '' : ' active' ?>" href="<?= e(url('groupes')) ?>">Tous les groupes</a></li>
  <li class="nav-item"><a class="nav-link<?= $mine ? ' active' : '' ?>" href="<?= e(url('groupes', ['onglet' => 'mes'])) ?>">Mes groupes</a></li>
</ul>
<form class="row g-2 mb-3" method="get" action="index.php">
  <input type="hidden" name="page" value="groupes"><input type="hidden" name="onglet" value="<?= $mine ? 'mes' : '' ?>">
  <div class="col-md-7"><input type="text" name="q" class="form-control" placeholder="Rechercher un groupe" value="<?= e($q) ?>"></div>
  <div class="col-md-3"><select name="statut" class="form-select"><?= statut_options($statut) ?></select></div>
  <div class="col-md-2 d-grid"><button class="btn btn-hc">Filtrer</button></div>
</form>
<div class="row g-3 mb-4">
<?php if (!$groupes): ?><p>Aucun groupe trouvé.</p><?php endif; ?>
<?php foreach ($groupes as $g): $own = $g->getIdCreateur() === $uid; ?>
  <div class="col-md-6 col-lg-4"><div class="panel p-3 h-100 d-flex flex-column">
    <div class="d-flex justify-content-between"><?= statut_badge($g->getStatut()) ?><small><i class="bi bi-people"></i> <?= $g->getNbMembres() ?></small></div>
    <h2 class="h5 mt-2"><?= e($g->getNom()) ?></h2>
    <p class="flex-grow-1"><?= e($g->getDescription()) ?></p>
    <?php if ($own && $g->getStatut() === 'rejete'): ?><small class="text-danger mb-2">Motif : <?= e($g->getMotifRejet()) ?></small><?php endif; ?>
    <small class="mb-2">Créé par <?= $own ? 'vous' : e($g->getNomCreateur()) ?></small>
    <div class="d-flex flex-wrap gap-1 align-items-center">
      <a href="<?= e(url('groupe-detail', ['id' => $g->getId()])) ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
      <?php if ($g->getEstMembre()): ?><span class="badge bg-info text-dark">Membre</span>
      <?php elseif ($g->getStatut() === 'approuve'): ?>
        <form method="post" action="<?= e(url('groupe-join')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $g->getId() ?>"><button class="btn btn-sm btn-hc">Rejoindre</button></form>
      <?php endif; ?>
      <?php if ($own): ?>
        <a href="<?= e(url('groupe-form', ['id' => $g->getId()])) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
        <form method="post" action="<?= e(url('groupe-delete')) ?>" onsubmit="return confirm('Supprimer ce groupe ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $g->getId() ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
      <?php endif; ?>
    </div>
  </div></div>
<?php endforeach; ?>
</div>
<?= pagination($page, $pages, 'groupes', $qs) ?>
