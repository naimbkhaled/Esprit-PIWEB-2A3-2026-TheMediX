<?php
/**
* views/user_space.php
* Expects: $user (UserModel)
*/
ob_start();
?>
<div class="container wide">
<h1>Welcome, <?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?> 👋</h1>
<p><span class="badge badge-user">Role: User</span></p>
<table>
<tr><th>ID</th><td><?= $user->getId() ?></td></tr>
<tr><th>Username</th><td><?= htmlspecialchars($user->getUsername(), ENT_QUOTES,
'UTF-8') ?></td></tr>
<tr><th>Email</th><td><?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8')
?></td></tr>
<tr><th>Member since</th><td><?= htmlspecialchars($user->getCreatedAt(),
ENT_QUOTES, 'UTF-8') ?></td></tr>
</table>
<p style="margin-top:24px; color:var(--chrome-text-light); font-size:14px;">
This is your personal space (frontoffice). Only administrators can access the
backoffice.
</p>
<p><a href="index.php?action=deactivate">Deactivate my account</a></p>
<p><a href="index.php?action=2fa">Two-factor authentication</a></p>
</div>
<?php
$content = ob_get_clean();
$title = 'My space - MyApp';
require __DIR__ . '/layout.php';