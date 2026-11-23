<?php
// VIEW front office : inscription client / médecin
require_once __DIR__ . '/../../controller/UserController.php';

$errors = []; $old = []; $success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $res = (new UserController())->register($_POST, $_FILES['preuve_identite'] ?? null);
    $errors = $res['errors'];
    $success = $res['ok'];
    if ($success) $old = [];
}
function v(array $o, string $k): string { return htmlspecialchars($o[$k] ?? '', ENT_QUOTES, 'UTF-8'); }
function err(array $e, string $k): string { return isset($e[$k]) ? htmlspecialchars($e[$k]) : ''; }
$role = $old['role'] ?? 'client';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Créer un compte</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<main class="card">
  <h1>Créer votre compte</h1>

  <?php if ($success): ?>
    <p class="ok">Compte créé avec succès<?= $_POST['role'] === 'medecin' ? ". Votre justificatif sera vérifié." : "." ?></p>
  <?php endif; ?>

  <form id="regForm" method="post" enctype="multipart/form-data" novalidate>
    <div class="role">
      <label><input type="radio" name="role" value="client" <?= $role === 'client' ? 'checked' : '' ?>> Client</label>
      <label><input type="radio" name="role" value="medecin" <?= $role === 'medecin' ? 'checked' : '' ?>> Médecin</label>
    </div>

    <div class="grid">
      <div class="field"><label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= v($old,'nom') ?>">
        <small class="err" data-for="nom"><?= err($errors,'nom') ?></small></div>

      <div class="field"><label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= v($old,'prenom') ?>">
        <small class="err" data-for="prenom"><?= err($errors,'prenom') ?></small></div>

      <div class="field"><label for="date_naissance">Date de naissance</label>
        <input type="date" id="date_naissance" name="date_naissance" value="<?= v($old,'date_naissance') ?>">
        <small class="err" data-for="date_naissance"><?= err($errors,'date_naissance') ?></small></div>

      <div class="field"><label for="genre">Genre</label>
        <select id="genre" name="genre">
          <option value="">Choisir</option>
          <option value="homme" <?= ($old['genre'] ?? '') === 'homme' ? 'selected' : '' ?>>Homme</option>
          <option value="femme" <?= ($old['genre'] ?? '') === 'femme' ? 'selected' : '' ?>>Femme</option>
        </select>
        <small class="err" data-for="genre"><?= err($errors,'genre') ?></small></div>

      <div class="field"><label for="email">Adresse e-mail</label>
        <input type="text" id="email" name="email" value="<?= v($old,'email') ?>">
        <small class="err" data-for="email"><?= err($errors,'email') ?></small></div>

      <div class="field"><label for="telephone">Téléphone</label>
        <input type="text" id="telephone" name="telephone" value="<?= v($old,'telephone') ?>">
        <small class="err" data-for="telephone"><?= err($errors,'telephone') ?></small></div>
    </div>

    <div class="field"><label for="hebergement">Lieu d'hébergement (facultatif)</label>
      <input type="text" id="hebergement" name="hebergement" value="<?= v($old,'hebergement') ?>">
      <small class="err" data-for="hebergement"><?= err($errors,'hebergement') ?></small></div>

    <fieldset id="doctorFields" class="doctor" <?= $role === 'medecin' ? '' : 'hidden' ?>>
      <legend>Informations professionnelles</legend>
      <div class="field"><label for="specialite">Spécialité</label>
        <input type="text" id="specialite" name="specialite" value="<?= v($old,'specialite') ?>">
        <small class="err" data-for="specialite"><?= err($errors,'specialite') ?></small></div>

      <div class="field"><label for="preuve_identite">Justificatif de médecin certifié (PDF, JPG, PNG – 5 Mo max)</label>
        <input type="file" id="preuve_identite" name="preuve_identite">
        <small class="err" data-for="preuve_identite"><?= err($errors,'preuve_identite') ?></small></div>
    </fieldset>

    <button type="submit">Créer mon compte</button>
  </form>
</main>
<script src="../../assets/js/validation.js"></script>
</body>
</html>
