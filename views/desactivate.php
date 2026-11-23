<?php
/**
 * views/deactivate.php
 * Expects: $errors (array), $csrfToken (string)
 */
ob_start();
?>
<div class="container">
    <h1>Deactivate my account</h1>
    <p>Your account will be disabled and you will be logged out. You will no longer be able to log in.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=deactivate"
          onsubmit="return confirm('Are you sure you want to deactivate your account?');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <label for="password">Confirm your password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit">Deactivate my account</button>
    </form>

    <div class="switch-link"><a href="index.php?action=dashboard">Cancel</a></div>
</div>
<?php
$content = ob_get_clean();
$title = 'Deactivate account - MyApp';
require __DIR__ . '/layout.php';