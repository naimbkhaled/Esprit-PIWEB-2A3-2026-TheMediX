<?php /* Titre, sous-titre et lien retour affichés dans l’en-tête de page. */ ?>

<section class="card">
  <div class="card-header">
    <h2>Groupes disponibles</h2>
  </div>
  <div class="tw"><table>
    <thead><tr><th>Groupe</th><th>Description</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($groupes as $g): ?>
      <tr>
        <td><?= e($g['nom']) ?> <?php if ($g['membre']): ?><span class="badge ok">membre</span><?php endif; ?></td>
        <td><?= e($g['description']) ?></td>
        <td>
          <form method="post" action="<?= url($g['membre'] ? '/groupes/quitter' : '/groupes/rejoindre') ?>">
            <?= csrf_field() ?><input type="hidden" name="groupe_id" value="<?= (int)$g['id'] ?>">
            <button class="btn sm <?= $g['membre'] ? 'del' : '' ?>" type="submit"><?= $g['membre'] ? 'Quitter' : 'Rejoindre' ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
