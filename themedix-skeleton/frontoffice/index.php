<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$routes = [
    'home'          => 'HomeController',
    'consultations' => 'ConsultationController',
    'messages'      => 'MessageController',
    'mesures'       => 'MesureController',
    'alertes'       => 'AlerteController',
    'medicaments'   => 'MedicamentController',
    'traitements'   => 'TraitementController',
    'allergies'     => 'AllergieController',
];

if (!isset($routes[$page])) {
    $page = 'home';
}

$controllerName = $routes[$page];
require_once 'Controllers/' . $controllerName . '.php';
$controller = new $controllerName();
$controller->index();