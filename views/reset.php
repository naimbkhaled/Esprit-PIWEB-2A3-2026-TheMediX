<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouveau mot de passe – Medix</title>
    <link rel="stylesheet" href="myapp1122/assets/style.css">
</head>
<body>
<main>
    <form class="glass" method="POST" action="index.php?action=reset" data-validate="reset" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
        <div class="c">
            <img src="myapp1122/assets/logo.png" alt="Medix" style="display:block;width:9rem;max-width:100%;height:auto;margin:0 auto 1rem">
            <h1>Nouveau mot de passe</h1>
            <p>Choisissez un mot de passe d'au moins 8 caractères</p>
        </div>
        <?php if (!$valid): ?>
            <div class="alert err">Ce lien est invalide ou a expiré.</div>
            <p class="c"><a href="index.php?action=forgot">Refaire une demande</a></p>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert err" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <input type="password" name="password" placeholder="Nouveau mot de passe" autocomplete="new-password">
            <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" autocomplete="new-password">
            <button class="orb" type="submit">Enregistrer</button>
        <?php endif; ?>
    </form>
</main>
<script src="myapp1122/assets/validate.js"></script>
</body>
</html>
