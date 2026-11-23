<?php
/**
* views/admin_space.php
* Backoffice dashboard - visually distinct from the frontoffice.
* Expects: $user (UserModel), $allUsers (UserModel[]), $totalUsers (int),
* $totalAdmins (int), $totalRegular (int)
*/
ob_start();
?>
<div class="stats-grid">
<div class="stat-card">
<div class="label">Total accounts</div>
<div class="value"><?= $totalUsers ?></div>
</div>
<div class="stat-card">
<div class="label">Regular users</div>
<div class="value accent"><?= $totalRegular ?></div>
</div>
<div class="stat-card">
<div class="label">Administrators</div>
<div class="value red"><?= $totalAdmins ?></div>
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
<th>Created at</th>
</tr>
</thead>
<tbody>
<?php foreach ($allUsers as $u): /** @var UserModel $u */ ?>
<tr>
<td><?= $u->getId() ?></td>
<td><?= htmlspecialchars($u->getUsername(), ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars($u->getEmail(), ENT_QUOTES, 'UTF-8') ?></td>
<td>
<span class="badge <?= $u->getRole() === 'admin' ? 'badge-admin' :
'badge-user' ?>">
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
$content = ob_get_clean();
$title = 'Dashboard';
$activePage = 'dashboard';
require __DIR__ . '/layout_admin.php';