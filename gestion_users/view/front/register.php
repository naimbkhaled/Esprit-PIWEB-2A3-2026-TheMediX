<?php
$old = $old ?? [];
$errors = $errors ?? [];
$escapeValue = static function (string $key) use ($old): string {
    $value = $old[$key] ?? '';
    return htmlspecialchars(is_string($value) ? $value : '', ENT_QUOTES, 'UTF-8');
};
$fieldError = static function (string $key) use ($errors): string {
    $value = $errors[$key] ?? '';
    return htmlspecialchars(is_string($value) ? $value : '', ENT_QUOTES, 'UTF-8');
};
$role = is_string($old['role'] ?? null) ? $old['role'] : 'client';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Créer un compte</title>
  <link rel="stylesheet" href="gestion_users/assets/css/style.css">
</head>
<body>
<main class="card">
  <h1>Créer votre compte</h1>
  <?php if (!empty($errors['general'])): ?>
    <p class="err" role="alert"><?= $fieldError('general') ?></p>
  <?php endif; ?>

  <form id="regForm" method="post" action="index.php?action=register" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <div class="role">
      <label><input type="radio" name="role" value="client" <?= $role === 'client' ? 'checked' : '' ?>> Client</label>
      <label><input type="radio" name="role" value="medecin" <?= $role === 'medecin' ? 'checked' : '' ?>> Médecin</label>
    </div>
    <small class="err"><?= $fieldError('role') ?></small>

    <div class="grid">
      <div class="field"><label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= $escapeValue('nom') ?>">
        <small class="err" data-for="nom"><?= $fieldError('nom') ?></small></div>

      <div class="field"><label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= $escapeValue('prenom') ?>">
        <small class="err" data-for="prenom"><?= $fieldError('prenom') ?></small></div>

      <div class="field"><label for="date_naissance">Date de naissance</label>
        <input type="date" id="date_naissance" name="date_naissance" value="<?= $escapeValue('date_naissance') ?>">
        <small class="err" data-for="date_naissance"><?= $fieldError('date_naissance') ?></small></div>

      <div class="field"><label for="genre">Genre</label>
        <select id="genre" name="genre">
          <option value="">Choisir</option>
          <option value="homme" <?= ($old['genre'] ?? '') === 'homme' ? 'selected' : '' ?>>Homme</option>
          <option value="femme" <?= ($old['genre'] ?? '') === 'femme' ? 'selected' : '' ?>>Femme</option>
        </select>
        <small class="err" data-for="genre"><?= $fieldError('genre') ?></small></div>

      <div class="field"><label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" autocomplete="email" value="<?= $escapeValue('email') ?>">
        <small class="err" data-for="email"><?= $fieldError('email') ?></small></div>

      <div class="field"><label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" value="<?= $escapeValue('telephone') ?>">
        <small class="err" data-for="telephone"><?= $fieldError('telephone') ?></small></div>
    </div>

    <div class="field"><label for="hebergement">Lieu d'hébergement (facultatif)</label>
      <input type="text" id="hebergement" name="hebergement" value="<?= $escapeValue('hebergement') ?>">
      <small class="err" data-for="hebergement"><?= $fieldError('hebergement') ?></small></div>

    <fieldset id="doctorFields" class="doctor" <?= $role === 'medecin' ? '' : 'hidden' ?>>
      <legend>Informations professionnelles</legend>
      <div class="field"><label for="specialite">Spécialité</label>
        <input type="text" id="specialite" name="specialite" value="<?= $escapeValue('specialite') ?>">
        <small class="err" data-for="specialite"><?= $fieldError('specialite') ?></small></div>

      <div class="field"><label for="preuve_identite">Justificatif de médecin certifié (PDF, JPG, PNG – 5 Mo max)</label>
        <input type="file" id="preuve_identite" name="preuve_identite" accept=".pdf,.jpg,.jpeg,.png">
        <small class="err" data-for="preuve_identite"><?= $fieldError('preuve_identite') ?></small></div>
    </fieldset>

    <div class="field"><label for="password">Mot de passe (8 caractères minimum)</label>
      <input type="password" id="password" name="password" autocomplete="new-password">
      <small class="err" data-for="password"><?= $fieldError('password') ?></small></div>
    <div class="field"><label for="password_confirm">Confirmer le mot de passe</label>
      <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password">
      <small class="err" data-for="password_confirm"><?= $fieldError('password_confirm') ?></small></div>

    <button type="submit">Créer mon compte</button>
  </form>
  <p>Déjà un compte ? <a href="index.php?action=login">Se connecter</a></p>
</main>
<script src="gestion_users/assets/js/validation.js"></script>
</body>
</html>
