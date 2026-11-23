<?php
/**
 * Expects: $errors, $csrfToken, $user (UserModel), $secret (string|null), $otpauth (string|null)
 */
ob_start();
?>
<div class="container">
    <h1>Two-factor authentication</h1>

    <?php if (!empty($_GET['done'])): ?>
        <div class="alert alert-success">Settings updated.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($user->isTwoFactorEnabled()): ?>
        <p><span class="badge badge-user">2FA is ON</span></p>
        <form method="POST" action="index.php?action=2fa">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="do" value="disable">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            <label for="code">Current 6-digit code</label>
            <input type="text" id="code" name="code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6">
            <button type="submit">Disable 2FA</button>
        </form>
    <?php else: ?>
        <p>1. Open your authenticator app and add an account <strong>manually</strong> with this key:</p>
        <p><code><?= htmlspecialchars(implode(' ', str_split($secret, 4)), ENT_QUOTES, 'UTF-8') ?></code></p>
        <p style="font-size:12px">Type: time-based. Link: <?= htmlspecialchars($otpauth, ENT_QUOTES, 'UTF-8') ?></p>
        <p>2. Enter the code the app shows to confirm:</p>
        <form method="POST" action="index.php?action=2fa">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="do" value="enable">
            <label for="code">6-digit code</label>
            <input type="text" id="code" name="code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6">
            <button type="submit">Enable 2FA</button>
        </form>
    <?php endif; ?>

    <div class="switch-link"><a href="index.php?action=dashboard">Back</a></div>
</div>
<?php
$content = ob_get_clean();
$title = 'Two-factor - MyApp';
require __DIR__ . '/layout.php';