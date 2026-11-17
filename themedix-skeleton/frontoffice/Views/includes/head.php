<?php
$base = preg_replace('#/Views$#', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
$assets = $base . '/Views/assets/';
$cssFiles = glob(__DIR__ . '/../assets/css/*.css');
usort($cssFiles, function ($a, $b) {
    return (basename($a) === 'modules.css') - (basename($b) === 'modules.css');
});
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars(isset($pageTitle) ? $pageTitle : 'The Medix') ?></title>
<?php foreach ($cssFiles as $f): ?>
<link href="<?= $assets ?>css/<?= basename($f) ?>" rel="stylesheet">
<?php endforeach; ?>