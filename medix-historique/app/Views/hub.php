<?php foreach ($groupes as $nomGroupe => $items): ?>
  <h2 class="grp"><?= e($nomGroupe) ?></h2>
  <div class="hub">
  <?php foreach ($items as $m): $n = (int)$m['stats']['n']; $unite = $m['unite'] ?? ['entrée', 'entrées']; ?>
    <a class="m" href="<?= url($m['url'] ?? '/h/' . $m['slug']) ?>">
      <b><?= e($m['titre']) ?></b>
      <span><?= $n ?> <small><?= e($n > 1 ? $unite[1] : $unite[0]) ?></small></span>
      <small><?= $m['stats']['d'] ? 'Dernière : ' . e(date_fr($m['stats']['d'])) : 'Aucune activité' ?></small>
      <?php if (empty($m['perm'])): ?><em>Consultation seule</em><?php endif; ?>
    </a>
  <?php endforeach; ?>
  </div>
<?php endforeach; ?>

<section class="card page-section">
  <div class="card-header">
    <h2>Mes données</h2>
  </div>
  <p>Téléchargez une copie de tout votre historique (format JSON). Chaque historique peut aussi être exporté en CSV depuis sa page.</p>
  <a class="btn" href="<?= url('/export') ?>">Exporter toutes mes données</a>
</section>
