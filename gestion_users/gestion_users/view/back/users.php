<?php
// VIEW back office : liste et suppression des utilisateurs
require_once __DIR__ . '/../../controller/UserController.php';
$ctrl = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $ctrl->delete((int) $_POST['delete_id']);
    header('Location: users.php');
    exit;
}
$users = $ctrl->getAll();
function h($s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Utilisateurs</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<main class="card wide">
  <h1>Utilisateurs inscrits</h1>
  <?php if (!$users): ?>
    <p>Aucun utilisateur pour le moment.</p>
  <?php else: ?>
  <div class="scroll">
  <table>
    <thead><tr><th>Nom</th><th>Prénom</th><th>Naissance</th><th>Genre</th><th>E-mail</th><th>Téléphone</th>
      <th>Hébergement</th><th>Type</th><th>Spécialité</th><th>Justificatif</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= h($u['nom']) ?></td><td><?= h($u['prenom']) ?></td><td><?= h($u['date_naissance']) ?></td>
        <td><?= h($u['genre']) ?></td><td><?= h($u['email']) ?></td><td><?= h($u['telephone']) ?></td>
        <td><?= h($u['hebergement']) ?></td><td><?= h($u['role']) ?></td><td><?= h($u['specialite']) ?></td>
        <td><?php if ($u['preuve_identite']): ?><a href="../../uploads/<?= h($u['preuve_identite']) ?>" target="_blank">Voir</a><?php endif; ?></td>
        <td>
          <form method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
            <input type="hidden" name="delete_id" value="<?= (int) $u['id'] ?>">
            <button type="submit" class="danger">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
