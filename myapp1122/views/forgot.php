<?php
/**
 * views/forgot.php
 * Expects: $errors (array), $old (array), $csrfToken (string), $sent (bool)
 */
ob_start();
$old = $old ?? [];
?>
<form class="glass" method="POST" action="index.php?action=forgot" data-validate="forgot" novalidate>
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
<div class="c"><img src="assets/logo.png" alt="The Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem"><h1>Mot de passe oublié</h1><p>Recevez un lien de réinitialisation par email</p></div>

<?php if (!empty($sent)): ?>
<div class="alert ok">Si un compte existe pour cet email, un lien de réinitialisation vient d'être envoyé (valable 60 minutes).</div>
<?php else: ?>
<div class="alert err" data-errors <?= empty($errors) ? 'hidden' : '' ?>>
<?php foreach ($errors as $error): ?>
<div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
</div>
<input type="email" name="email" placeholder="Email" autocomplete="email"
       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<button class="orb">Envoyer le lien</button>
<?php endif; ?>

<p class="c"><a href="index.php?action=login">← Retour à la connexion</a></p>
</form>
<?php
$content = ob_get_clean();
$title = 'Nébula — Mot de passe oublié';
require __DIR__ . '/layout.php';
