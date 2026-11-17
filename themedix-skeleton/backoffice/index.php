<?php
require_once 'Controllers/Controller.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$routes = [
    'dashboard'   => 'DashboardController',
    'medicaments' => 'MedicamentController',
    'traitements' => 'TraitementController',
    'allergies'   => 'AllergieController',
];

if (!isset($routes[$page])) {
    $page = 'dashboard';
}

require_once 'Controllers/' . $routes[$page] . '.php';
$controller = new $routes[$page]();
$controller->index();