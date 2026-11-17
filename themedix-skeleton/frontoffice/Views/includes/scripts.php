<?php
$base = preg_replace('#/Views$#', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
$assets = $base . '/Views/assets/';
foreach (glob(__DIR__ . '/../assets/js/*.js') as $f): ?>
<script src="<?= $assets ?>js/<?= basename($f) ?>"></script>
<?php endforeach; ?>
<script>
document.querySelectorAll('[data-toggle]').forEach(function (b) {
  b.addEventListener('click', function () {
    var t = document.querySelector(b.dataset.toggle);
    t.hidden = !t.hidden;
  });
});
</script>
