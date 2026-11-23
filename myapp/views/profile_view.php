<?php
ob_start();
?>
<div style="max-width:960px;margin:32px auto;padding:0 24px;">
    <!-- Profile banner card -->
    <div class="profile-card">
        <div class="profile-banner"></div>
        <div class="profile-info">
            <div class="profile-left">
                <div class="avatar-wrap">
                    <div class="avatar">
                        <?= strtoupper(substr($user->getUsername(), 0, 1)) ?>
                    </div>
                </div>
                <div>
                    <div class="profile-name"><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="profile-handle">@<?= htmlspecialchars(strtolower($user->getUsername()), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>
            <a class="btn-link" href="index.php?action=profile_edit">⚙️ Account settings</a>
        </div>
        <div class="profile-tabs">
            <div class="profile-tab active">📋 Bookmarked</div>
            <div class="profile-tab">📊 My data</div>
            <div class="profile-tab">🔔 Reminders</div>
        </div>
    </div>

    <?php if (!empty($_GET['updated'])): ?>
        <div class="alert alert-success">✅ Your information has been updated.</div>
    <?php endif; ?>

    <div class="card">
        <h2>My information</h2>
        <p style="margin-bottom:16px;">
            <span class="badge <?= $user->getRole() === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                <?= $user->getRole() === 'admin' ? '⚙️' : '🩺' ?>
                <?= htmlspecialchars($user->getRole(), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </p>
        <table>
            <tr><th>ID</th><td><?= $user->getId() ?></td></tr>
            <tr><th>Username</th><td><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Member since</th><td><?= htmlspecialchars($user->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        </table>
        <a class="btn-link" href="index.php?action=profile_edit">✏️ Edit my information</a>
    </div>
</div>
<?php
$content    = ob_get_clean();
$title      = 'My profile – Medix';
$activePage = 'profile';
$layoutFile = ($_SESSION['role'] ?? '') === 'admin' ? 'layout_admin.php' : 'layout.php';
require __DIR__ . '/' . $layoutFile;
