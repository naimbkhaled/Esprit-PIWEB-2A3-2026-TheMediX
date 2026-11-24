<?php
/**
 * views/reset.php
 * Expects: $errors (array), $csrfToken (string), $token (string), $valid (bool)
 */
ob_start();
?>
<form class="glass" method="POST" action="index.php?action=reset" data-validate="reset" novalidate>
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
<div class="c"><img src="assets/logo.png" alt="The Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem"><h1>Nouveau mot de passe</h1><p>Choisissez un mot de passe d'au moins 8 caractères</p></div>

<?php if (!$valid): ?>
<div class="alert err">Ce lien est invalide ou a expiré.</div>
<p class="c"><a href="index.php?action=forgot">Refaire une demande</a></p>
<?php else: ?>
<div class="alert err" data-errors <?= empty($errors) ? 'hidden' : '' ?>>
<?php foreach ($errors as $error): ?>
<div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
</div>
<input type="password" name="password" placeholder="Nouveau mot de passe" autocomplete="new-password">
<input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" autocomplete="new-password">
<button class="orb">Enregistrer</button>
<?php endif; ?>
</form>
<?php
$content = ob_get_clean();
$title = 'Nébula — Nouveau mot de passe';
require __DIR__ . '/layout.php';
