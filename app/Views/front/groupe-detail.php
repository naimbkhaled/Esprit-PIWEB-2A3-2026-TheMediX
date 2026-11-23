<a href="<?= e(url('groupes')) ?>" class="text-hc text-decoration-none">&larr; Retour aux groupes</a>
<div class="panel p-4 my-3 d-flex flex-wrap justify-content-between">
  <div><h1 class="h4"><?= e($groupe->getNom()) ?></h1><p class="mb-1"><?= e($groupe->getDescription()) ?></p><?= statut_badge($groupe->getStatut()) ?> <small><?= $groupe->getNbMembres() ?> membres, créé par <?= e($groupe->getNomCreateur()) ?></small></div>
  <?php if (!$groupe->getEstMembre() && $groupe->getStatut() === 'approuve'): ?>
    <form method="post" action="<?= e(url('groupe-join')) ?>" class="align-self-start"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $groupe->getId() ?>"><button class="btn btn-hc">Rejoindre le groupe</button></form>
  <?php endif; ?>
</div>
<div class="row g-3">
  <div class="col-lg-7"><div class="panel p-3 h-100"><h2 class="h6">Règles du groupe</h2><p class="mb-0" style="white-space:pre-line"><?= e($groupe->getRegles()) ?></p></div></div>
  <div class="col-lg-5">
    <?php if ($groupe->getEstMembre() && $groupe->getStatut() === 'approuve'): ?>
    <div class="panel p-3 mb-3"><h2 class="h6">Inviter un membre</h2>
      <form method="post" action="<?= e(url('groupe-invite')) ?>" onsubmit="return validerInvitation()" novalidate>
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $groupe->getId() ?>">
        <div class="mb-2"><input id="invite" name="invite" type="text" class="form-control" placeholder="Email ou identifiant du patient"><div class="invalid-feedback" id="err-invite"></div></div>
        <button class="btn btn-hc btn-sm">Envoyer l’invitation</button>
      </form></div>
    <?php endif; ?>
    <div class="panel p-3"><h2 class="h6">Membres (<?= count($membres) ?>)</h2><ul class="mb-0"><?php foreach ($membres as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div>
  </div>
</div>
