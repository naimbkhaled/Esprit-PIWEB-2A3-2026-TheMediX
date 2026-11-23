<?php
ob_start();
$old = $old ?? [];
?>
<div class="container">
    <h1>✏️ Edit my information</h1>
    <p class="form-subtitle">Update your Medix account details below</p>

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

    <form method="POST" action="index.php?action=profile_edit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required
               value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <fieldset>
            <legend>Change password (optional)</legend>
            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password" placeholder="••••••••">
            <label for="new_password">New password <span style="font-weight:400;text-transform:none;">(min. 8 characters)</span></label>
            <input type="password" id="new_password" name="new_password" autocomplete="new-password" placeholder="••••••••">
            <label for="new_password_confirm">Confirm new password</label>
            <input type="password" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password" placeholder="••••••••">
        </fieldset>
        <button type="submit">Save changes →</button>
    </form>

    <div class="switch-link">
        <a href="index.php?action=profile">← Cancel</a>
    </div>
</div>
<?php
$content    = ob_get_clean();
$title      = 'Edit profile – Medix';
$activePage = 'profile';
$layoutFile = ($_SESSION['role'] ?? '') === 'admin' ? 'layout_admin.php' : 'layout.php';
require __DIR__ . '/' . $layoutFile;
