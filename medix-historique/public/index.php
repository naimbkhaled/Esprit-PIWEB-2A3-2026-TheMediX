<?php
declare(strict_types=1);
// Serveur de dev : php -S localhost:8000 -t public public/index.php
if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) return false;

define('APP_PATH', dirname(__DIR__) . '/app');
spl_autoload_register(function (string $class): void {
    if (strncmp($class, 'App\\', 4) !== 0) return;
    $file = APP_PATH . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) require $file;
});
require APP_PATH . '/Core/helpers.php';

// INTÉGRATION : si l'application globale a déjà démarré la session, cette ligne ne fait rien.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

use App\Core\Router;

$r = new Router();
$r->get('/',                             'HistoriqueController@hub');
$r->get('/h/{module}',                   'HistoriqueController@liste');
$r->post('/h/{module}/enregistrer',      'HistoriqueController@enregistrer');
$r->post('/h/{module}/effacer-tout',     'HistoriqueController@effacerTout');
$r->post('/h/{module}/{id}/supprimer',   'HistoriqueController@supprimer');
$r->get('/h/{module}/export',            'ExportController@csv');
$r->get('/export',                       'ExportController@json');
$r->get('/partage',                      'PartageController@index');
$r->post('/partage',                     'PartageController@enregistrer');
$r->get('/groupes',                      'GroupeController@index');
$r->post('/groupes/rejoindre',           'GroupeController@rejoindre');
$r->post('/groupes/quitter',             'GroupeController@quitter');
// Outils de développement (désactivés si dev_mode = false)
$r->get('/dev/connexion',                'DevController@form');
$r->post('/dev/connexion',               'DevController@connecter');
$r->get('/dev/deconnexion',              'DevController@deconnecter');

$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
