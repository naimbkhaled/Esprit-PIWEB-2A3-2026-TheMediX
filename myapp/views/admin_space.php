<?php
ob_start();
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total accounts</div>
        <div class="value"><?= $totalUsers ?></div>
        <div class="trend">👤 All registered users</div>
    </div>
    <div class="stat-card">
        <div class="label">Regular users</div>
        <div class="value accent"><?= $totalRegular ?></div>
        <div class="trend">🩺 Patients & members</div>
    </div>
    <div class="stat-card">
        <div class="label">Administrators</div>
        <div class="value red"><?= $totalAdmins ?></div>
        <div class="trend">⚙️ Admin accounts</div>
    </div>
</div>

<div class="card">
    <h2>All users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allUsers as $u): ?>
                <tr>
                    <td><?= $u->getId() ?></td>
                    <td><?= htmlspecialchars($u->getUsername(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($u->getEmail(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge <?= $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                            <?= htmlspecialchars($u->getRole(), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($u->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content    = ob_get_clean();
$title      = 'Dashboard – Medix Admin';
$activePage = 'dashboard';
require __DIR__ . '/layout_admin.php';
