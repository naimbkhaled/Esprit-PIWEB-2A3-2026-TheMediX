<?php
return [
    'db' => [
        'dsn'  => 'mysql:host=localhost;dbname=medix;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
    ],
    // true = page de connexion de test (/dev/connexion). À mettre à false à l'intégration.
    'dev_mode'    => true,
    // Clé de session contenant l'identifiant de l'utilisateur connecté (voir Core/Auth.php)
    'session_key' => 'uid',
    // Où envoyer un visiteur non connecté (chemin interne au module, ou URL de l'application globale)
    'login_url'   => '/dev/connexion',
    // Liens vers les autres parties de l'application : [['Accueil', '/'], ['Glycémie', '/glycemie']]
    'nav'         => [],
];
