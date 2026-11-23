<?php
/**
 * views/layout.php
 * FRONTOFFICE layout = "Nébula" design (design login).
 * Expects: $title (string), $content (string, already generated/escaped)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Nébula', ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>
<main>
<button id="t" class="glass">☾ Sombre</button>
<?= $content ?>
</main>
<script src="assets/script.js"></script>
<script src="assets/validate.js"></script>
</body>
</html>
