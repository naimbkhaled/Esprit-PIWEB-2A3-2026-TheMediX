<?php
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié – Medix</title>
    <link rel="stylesheet" href="myapp1122/assets/style.css">
</head>
<body>
<main>
    <form class="glass" method="POST" action="index.php?action=forgot" data-validate="forgot" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="c">
            <img src="myapp1122/assets/logo.png" alt="Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem">
            <h1>Mot de passe oublié</h1>
            <p>Recevez un lien de réinitialisation par email</p>
        </div>
        <?php if (!empty($sent)): ?>
            <div class="alert ok">Si un compte existe pour cet email, un lien de réinitialisation vient d'être envoyé (valable 60 minutes).</div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert err" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <input type="email" name="email" placeholder="Email" autocomplete="email"
                   value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button class="orb" type="submit">Envoyer le lien</button>
        <?php endif; ?>
        <p class="c"><a href="index.php?action=login">← Retour à la connexion</a></p>
    </form>
</main>
<script src="myapp1122/assets/validate.js"></script>
</body>
</html>
