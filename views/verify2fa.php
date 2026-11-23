<?php
/** Expects: $errors (array), $csrfToken (string) */
ob_start();
?>
<div class="container">
    <h1>Two-factor verification</h1>
    <p>Enter the 6-digit code from your authenticator app.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=verify2fa">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="code">Verification code</label>
        <input type="text" id="code" name="code" required inputmode="numeric"
               pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus>
        <button type="submit">Verify</button>
    </form>

    <div class="switch-link"><a href="index.php?action=logout">Cancel</a></div>
</div>
<?php
$content = ob_get_clean();
$title = 'Verification - MyApp';
require __DIR__ . '/layout.php';