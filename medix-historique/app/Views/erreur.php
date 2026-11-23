<section class="card">
  <div class="card-header">
    <h2>Une erreur est survenue</h2>
  </div>
  <p class="empty" style="padding:0 0 1.25rem;text-align:left;color:var(--heading);font-weight:600;"><?= e($message ?? "Une erreur s'est produite.") ?></p>
  <a class="btn" href="<?= url('/') ?>">Retour à mon historique</a>
</section>
