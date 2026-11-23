<?php
ob_start();
?>
<div class="container">
    <div style="text-align:center;margin-bottom:28px;">
        <div style="font-size:40px;margin-bottom:10px;">🩺</div>
        <h1>Welcome back</h1>
        <p class="form-subtitle">Log in to your Medix account</p>
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

    <?php if (!empty($_GET['registered'])): ?>
        <div class="alert alert-success">✅ Account created — you can now log in.</div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required autocomplete="email" placeholder="you@example.com">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        <button type="submit">Log in →</button>
    </form>

    <div class="switch-link">
        Don't have an account? <a href="index.php?action=register">Create one for free</a>
    </div>
</div>
<?php
$content = ob_get_clean();
$title   = 'Log in – Medix';
require __DIR__ . '/layout.php';
