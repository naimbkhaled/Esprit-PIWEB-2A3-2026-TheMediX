<?php
/**
 * views/login.php
 * Expects: $errors (array), $old (array), $csrfToken (string), $providers (array)
 */
ob_start();
$old = $old ?? [];
?>
<form class="glass" method="POST" action="index.php?action=login" data-validate="login" novalidate>
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
<div class="c"><img src="assets/logo.png" alt="The Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem"><h1>Bienvenue</h1><p>Connectez-vous à votre espace</p></div>

<?php if (!empty($_GET['registered'])): ?>
<div class="alert ok">Compte créé avec succès, vous pouvez vous connecter.</div>
<?php endif; ?>
<?php if (!empty($_GET['reset'])): ?>
<div class="alert ok">Mot de passe modifié, vous pouvez vous connecter.</div>
<?php endif; ?>
<div class="alert err" data-errors <?= empty($errors) ? 'hidden' : '' ?>>
<?php foreach ($errors as $error): ?>
<div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
</div>

<input type="email" name="email" placeholder="Email" autocomplete="email"
       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<input type="password" name="password" placeholder="Mot de passe" autocomplete="current-password">
<div class="row">
<label><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
<a href="index.php?action=forgot">Mot de passe oublié ?</a>
</div>
<button class="orb">Se connecter</button>

<?php require __DIR__ . '/social.php'; ?>

<p class="c">Pas de compte ? <a href="index.php?action=register">Créer un compte</a></p>
</form>
<?php
$content = ob_get_clean();
$title = 'themedix — Connexion';
require __DIR__ . '/layout.php';
