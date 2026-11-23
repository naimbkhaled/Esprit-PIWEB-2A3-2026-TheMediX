<?php
/** Expects: $errors (array), $csrfToken (string), $sent (bool) */
ob_start();
?>
<div class="container">
    <h1>Forgot password</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($sent)): ?>
        <div class="alert alert-success">
            If this email belongs to an account, a reset link has been sent.
        </div>
    <?php else: ?>
        <form method="POST" action="index.php?action=forgot">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" required autocomplete="email">
            <button type="submit">Send reset link</button>
        </form>
    <?php endif; ?>

    <div class="switch-link"><a href="index.php?action=login">Back to log in</a></div>
</div>
<?php
$content = ob_get_clean();
$title = 'Forgot password - MyApp';
require __DIR__ . '/layout.php';