<?php
ob_start();
$old = $old ?? [];
?>
<div class="container">
    <div style="text-align:center;margin-bottom:28px;">
        <div style="font-size:40px;margin-bottom:10px;">💙</div>
        <h1>Create your account</h1>
        <p class="form-subtitle">Join Medix and take control of your diabetes health</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            ⚠️
            <div>
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=register">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required placeholder="yourname"
               value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com"
               value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <label for="password">Password <span style="font-weight:400;text-transform:none;">(min. 8 characters)</span></label>
        <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="••••••••">
        <label for="password_confirm">Confirm password</label>
        <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password" placeholder="••••••••">
        <button type="submit">Create my account →</button>
    </form>

    <div class="switch-link">
        Already have an account? <a href="index.php?action=login">Log in</a>
    </div>
</div>
<?php
$content = ob_get_clean();
$title   = 'Sign up – Medix';
require __DIR__ . '/layout.php';
