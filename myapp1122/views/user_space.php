<?php
/**
 * views/user_space.php
 * Expects: $user (UserModel)
 */
ob_start();
?>
<section class="glass card">
<div class="c"><img src="assets/logo.png" alt="The Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem">
<h1>Bienvenue, <?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?> 👋</h1>
<p>Votre espace personnel (frontoffice)</p></div>

<table>
<tr><th>ID</th><td><?= $user->getId() ?></td></tr>
<tr><th>Nom d'utilisateur</th><td><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Email</th><td><?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Rôle</th><td><?= htmlspecialchars($user->getRole(), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Connexion</th><td><?= htmlspecialchars($user->getProvider() ?: 'Email / mot de passe', ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Membre depuis</th><td><?= htmlspecialchars($user->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?></td></tr>
</table>

<a class="btn orb" href="index.php?action=logout">Se déconnecter</a>
</section>
<?php
$content = ob_get_clean();
$title = 'Nébula — Mon espace';
require __DIR__ . '/layout.php';
