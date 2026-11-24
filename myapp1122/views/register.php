<?php
/**
 * views/register.php
 * Expects: $errors (array), $old (array), $csrfToken (string), $providers (array)
 */
ob_start();
$old = $old ?? [];
?>
<form class="glass" method="POST" action="index.php?action=register" data-validate="register" novalidate>
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
<div class="c"><img src="assets/logo.png" alt="The Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem"><h1>Créer un compte</h1><p>Rejoignez-nous en quelques secondes</p></div>

<div class="alert err" data-errors <?= empty($errors) ? 'hidden' : '' ?>>
<?php foreach ($errors as $error): ?>
<div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
</div>

<input type="text" name="username" placeholder="Nom d'utilisateur" autocomplete="username"
       value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<input type="email" name="email" placeholder="Email" autocomplete="email"
       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<input type="password" name="password" placeholder="Mot de passe (8 caractères min.)" autocomplete="new-password">
<input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" autocomplete="new-password">
<button class="orb">Créer mon compte</button>

<?php require __DIR__ . '/social.php'; ?>

<p class="c">Déjà un compte ? <a href="index.php?action=login">Se connecter</a></p>
</form>
<?php
$content = ob_get_clean();
$title = 'Nébula — Inscription';
require __DIR__ . '/layout.php';
