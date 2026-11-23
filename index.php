<?php
// ===== POINT D'ENTRÉE + ROUTEUR : index.php?page=... =====
session_start();
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/helpers.php';
spl_autoload_register(function ($classe) {
    foreach (['Controllers', 'Models'] as $dossier) {
        $f = __DIR__ . "/app/$dossier/$classe.php";
        if (is_file($f)) { require_once $f; return; }
    }
});
$routes = [
    // patient (front office)
    'home'           => ['GroupeController', 'home'],
    'groupes'        => ['GroupeController', 'index'],
    'groupes-export' => ['GroupeController', 'export'],
    'groupe-form'    => ['GroupeController', 'form'],
    'groupe-save'    => ['GroupeController', 'save'],
    'groupe-delete'  => ['GroupeController', 'delete'],
    'groupe-join'    => ['GroupeController', 'join'],
    'groupe-detail'  => ['GroupeController', 'detail'],
    'groupe-invite'  => ['GroupeController', 'invite'],
    // admin (back office)
    'admin-login'         => ['AdminController', 'login'],
    'admin-logout'        => ['AdminController', 'logout'],
    'admin-groupes'       => ['AdminController', 'index'],
    'admin-groupe-detail' => ['AdminController', 'detail'],
    'admin-approve'       => ['AdminController', 'approve'],
    'admin-reject'        => ['AdminController', 'reject'],
    'admin-export'        => ['AdminController', 'export'],
];
$page = $_GET['page'] ?? 'home';
if (!isset($routes[$page])) { http_response_code(404); die('Page introuvable.'); }
[$controleur, $methode] = $routes[$page];
(new $controleur())->$methode();
