<?php use App\Core\{Auth, Config, Session}; $u = Auth::id() ? Auth::user() : null; ?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titre ?? 'Mon historique') ?> – MediX</title>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/medix-historique.css') ?>">
</head>
<body>
<header class="site-header"><div class="site-header__inner">
  <a class="logo" href="<?= url('/') ?>">Medi<span>X</span></a>
  <nav class="nav" aria-label="Navigation principale">
    <a href="<?= url('/') ?>" aria-current="page">Mon historique</a>
    <?php foreach ((array)Config::get('nav', []) as [$label, $href]): ?><a href="<?= e(url($href)) ?>"><?= e($label) ?></a><?php endforeach; ?>
  </nav>
  <span class="who"><?php if ($u): ?><?= e($u['nom']) ?><?php if (Config::get('dev_mode')): ?> · <a href="<?= url('/dev/deconnexion') ?>">Déconnexion</a><?php endif; ?><?php endif; ?></span>
</div></header>
<main>
  <?php foreach (Session::prendreFlash() as [$type, $msg]): ?><div class="alert <?= $type === 'ok' ? 'ok' : '' ?>"><?= e($msg) ?></div><?php endforeach; ?>
  <?= $content ?>
</main>
</body>
</html>
