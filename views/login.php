<?php
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion – Medix</title>
    <link rel="stylesheet" href="myapp1122/assets/style.css">
</head>
<body>
<main>
    <button id="t" class="glass">☾ Sombre</button>
    <form class="glass" method="POST" action="index.php?action=login" data-validate="login" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="c">
            <img src="myapp1122/assets/logo.png" alt="Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem">
            <h1>Bienvenue</h1>
            <p>Connectez-vous à votre espace</p>
        </div>

        <?php if (!empty($_GET['registered'])): ?>
            <div class="alert ok">Compte créé avec succès, vous pouvez vous connecter.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['deactivated'])): ?>
            <div class="alert ok">Votre compte a été désactivé.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['reset'])): ?>
            <div class="alert ok">Mot de passe modifié, vous pouvez vous connecter.</div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert err" role="alert">
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" autocomplete="email"
               value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="password" name="password" placeholder="Mot de passe" autocomplete="current-password">
        <div class="row">
            <label><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
            <a href="index.php?action=forgot">Mot de passe oublié ?</a>
        </div>
        <button class="orb" type="submit">Se connecter</button>
        <?php require __DIR__ . '/../myapp1122/views/social.php'; ?>
        <p class="c">Pas de compte ? <a href="index.php?action=register">Créer un compte</a></p>
    </form>
</main>
<script src="myapp1122/assets/script.js"></script>
<script src="myapp1122/assets/validate.js"></script>
</body>
</html>
