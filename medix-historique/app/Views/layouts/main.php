<?php
use App\Core\{Auth, Config, Session};
$u = Auth::id() ? Auth::user() : null;
$sousTitre = $sousTitre ?? ($sub ?? '');
$retour = $retour ?? null;
$navItems = (array)Config::get('nav', []);
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titre ?? 'Mon historique') ?> – MediX</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/medix-historique.css') ?>">
</head>
<body>
<nav class="navbar">
  <div class="container navbar-inner">
    <a class="navbar-brand" href="<?= url('/') ?>">Medi<span>X</span></a>
    <div class="navbar-collapse">
      <ul class="navbar-nav">
        <li><a class="nav-link is-active" href="<?= url('/') ?>">Mon historique</a></li>
        <?php foreach ($navItems as [$label, $href]): ?>
          <li><a class="nav-link" href="<?= e($href) ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="navbar-right">
      <?php if ($u): ?>
        <div class="user-menu">
          <span class="avatar avatar-sm"><?= e(mb_strtoupper(mb_substr($u['nom'], 0, 1))) ?></span>
          <span class="user-name"><?= e($u['nom']) ?></span>
          <?php if (Config::get('dev_mode')): ?>
            <a class="nav-link nav-link-muted" href="<?= url('/dev/deconnexion') ?>">Déconnexion</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main>
  <section class="profile-header">
    <div class="container">
      <div class="profile-cover"></div>
      <div class="card profile-card">
        <div class="profile-card-body">
          <div class="profile-main">
            <?php if ($u): ?><div class="avatar avatar-xl profile-avatar"><?= e(mb_strtoupper(mb_substr($u['nom'], 0, 1))) ?></div><?php endif; ?>
            <div class="profile-text">
              <?php if (is_array($retour) && !empty($retour['url'])): ?>
                <a class="back-link" href="<?= e(url((string)$retour['url'])) ?>">&larr; <?= e($retour['label'] ?? 'Retour') ?></a>
              <?php endif; ?>
              <h1 class="profile-title"><?= e($titre ?? 'Mon historique') ?></h1>
              <?php if ($sousTitre !== ''): ?><p class="profile-subtitle"><?= e($sousTitre) ?></p><?php endif; ?>
            </div>
          </div>
          <div class="profile-actions">
            <?php if ($u): ?><span class="badge badge-soft">Compte actif</span><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="page-content">
    <div class="container">
      <?php foreach (Session::prendreFlash() as [$type, $msg]): ?>
        <div class="alert <?= $type === 'ok' ? 'alert-success' : 'alert-danger' ?>" role="alert"><?= e($msg) ?></div>
      <?php endforeach; ?>
      <?= $content ?>
    </div>
  </section>
</main>
</body>
</html>
