<?php
ob_start();
?>

<!-- Profile banner -->
<div style="max-width:960px;margin:32px auto;padding:0 24px;">
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
            <div class="profile-tab active">📋 Overview</div>
            <div class="profile-tab">📊 My data</div>
            <div class="profile-tab">🔔 Reminders</div>
        </div>
    </div>

    <?php if (!empty($_GET['updated'])): ?>
        <div class="alert alert-success">✅ Your information has been updated.</div>
    <?php endif; ?>

    <!-- Dashboard cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px;">
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#e0f7f6;">📈</div>
            <h3>Glucose Log</h3>
            <p>Track and review your daily blood sugar readings.</p>
        </div>
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#fef4e0;">💊</div>
            <h3>Medications</h3>
            <p>Manage your insulin doses and medication schedule.</p>
        </div>
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#fceaea;">🥗</div>
            <h3>Nutrition</h3>
            <p>Log meals and monitor your carbohydrate intake.</p>
        </div>
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#dff5ec;">📅</div>
            <h3>Appointments</h3>
            <p>Upcoming consultations with your care team.</p>
        </div>
    </div>

    <!-- Profile info table -->
    <div class="card">
        <h2>My information</h2>
        <table>
            <tr><th>ID</th><td><?= $user->getId() ?></td></tr>
            <tr><th>Username</th><td><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Role</th><td>
                <span class="badge <?= $user->getRole() === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                    <?= htmlspecialchars($user->getRole(), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </td></tr>
            <tr><th>Member since</th><td><?= htmlspecialchars($user->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'My space – Medix';
require __DIR__ . '/layout.php';
