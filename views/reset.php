<?php
/** Expects: $errors (array), $csrfToken (string), $token (string), $validToken (bool) */
ob_start();
?>
<div class="container">
    <h1>Choose a new password</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($validToken): ?>
        <form method="POST" action="index.php?action=reset">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

            <label for="password">New password (min. 8 characters)</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">

            <label for="password_confirm">Confirm password</label>
            <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">

            <button type="submit">Change password</button>
        </form>
    <?php else: ?>
        <div class="switch-link"><a href="index.php?action=forgot">Request a new link</a></div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$title = 'Reset password - MyApp';
require __DIR__ . '/layout.php';