<?php
$perm = $mod['perm'];
$peutEditer = in_array('edit', $perm, true);
$peutSuppr  = in_array('delete', $perm, true);
$montrerForm = in_array('add', $perm, true) || $edit !== null;
$aDate = !empty($mod['date']);
$recherche = \App\Models\Historique::rechercheActive($mod);
$qs = http_build_query(array_filter($f));
?>
<p><a href="<?= url('/') ?>">← Tout mon historique</a></p>
<h1><?= e($mod['titre']) ?></h1>
<?php if (!empty($mod['note'])): ?><p class="sub"><?= e($mod['note']) ?></p><?php else: ?><p class="sub">&nbsp;</p><?php endif; ?>

<?php if ($montrerForm): ?>
<section class="card" id="saisie">
  <h2><?= $edit ? 'Modifier' : 'Ajouter' ?></h2>
  <form class="grid" method="post" action="<?= url('/h/' . $slug . '/enregistrer') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <?php foreach ($mod['champs'] as $c):
        if (($c['form'] ?? true) === false) continue;
        $d = $c['defaut'] ?? '';
        if ($d === 'now') $d = date('Y-m-d H:i:s');
        echo champ_html($c, $edit[$c['nom']] ?? $d, $opts[$c['nom']] ?? []);
    endforeach; ?>
    <div class="full row-actions">
      <button class="btn" type="submit">Enregistrer</button>
      <?php if ($edit): ?><a class="btn ghost" href="<?= url('/h/' . $slug) ?>">Annuler</a><?php endif; ?>
    </div>
  </form>
</section>
<?php endif; ?>

<section class="card">
  <h2>Historique</h2>
  <form class="grid" method="get" style="margin-bottom:18px">
    <?php if ($aDate): ?>
      <div><label for="du">Du</label><input id="du" name="du" type="date" value="<?= e($f['du']) ?>"></div>
      <div><label for="au">Au</label><input id="au" name="au" type="date" value="<?= e($f['au']) ?>"></div>
    <?php endif; ?>
    <?php if ($recherche): ?>
      <div><label for="q">Mot-clé</label><input id="q" name="q" type="search" value="<?= e($f['q']) ?>"></div>
    <?php endif; ?>
    <div class="row-actions">
      <?php if ($aDate || $recherche): ?>
        <button class="btn ghost" type="submit">Filtrer</button>
        <a class="btn ghost" href="<?= url('/h/' . $slug) ?>">Réinitialiser</a>
      <?php endif; ?>
      <a class="btn" href="<?= url('/h/' . $slug . '/export') . ($qs ? '?' . e($qs) : '') ?>">Exporter en CSV</a>
    </div>
  </form>

  <?php if (!empty($mod['effacer_tout']) && $rows): ?>
  <form method="post" action="<?= url('/h/' . $slug . '/effacer-tout') ?>" style="margin-bottom:14px" onsubmit="return confirm('Effacer tout cet historique ?')">
    <?= csrf_field() ?><button class="btn sm del" type="submit">Tout effacer</button>
  </form>
  <?php endif; ?>

  <?php if (!$rows): ?>
    <p class="empty">Aucune entrée.</p>
  <?php else: ?>
  <div class="tw"><table>
    <thead><tr>
      <?php foreach ($mod['champs'] as $c): ?><th><?= e($c['label']) ?></th><?php endforeach; ?>
      <?php if (isset($mod['badge'])): ?><th><?= e($mod['badge_titre'] ?? 'Statut') ?></th><?php endif; ?>
      <?php if ($peutEditer || $peutSuppr): ?><th></th><?php endif; ?>
    </tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <?php foreach ($mod['champs'] as $c): ?><td><?= e(fmt_val($c, $r[$c['nom']] ?? null, $opts[$c['nom']] ?? [])) ?></td><?php endforeach; ?>
        <?php if (isset($mod['badge'])): [$cls, $txt] = ($mod['badge'])($r); ?><td><span class="badge <?= e($cls) ?>"><?= e($txt) ?></span></td><?php endif; ?>
        <?php if ($peutEditer || $peutSuppr): ?>
        <td><div class="row-actions">
          <?php if ($peutEditer): ?><a class="btn sm ghost" href="<?= url('/h/' . $slug) . '?edit=' . (int)$r['id'] ?>#saisie">Modifier</a><?php endif; ?>
          <?php if ($peutSuppr): ?>
          <form method="post" action="<?= url('/h/' . $slug . '/' . (int)$r['id'] . '/supprimer') ?>" onsubmit="return confirm('Supprimer cette entrée ?')">
            <?= csrf_field() ?><button class="btn sm del" type="submit">Supprimer</button>
          </form>
          <?php endif; ?>
        </div></td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
