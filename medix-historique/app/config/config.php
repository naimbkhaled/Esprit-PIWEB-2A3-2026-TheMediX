<?php
return [
    'db' => [
        'dsn'  => 'mysql:host=localhost;dbname=session;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
    ],
    // false = intégration réelle avec Medix (désactive /dev/connexion)
    'dev_mode'    => false,
    // Clé de session de l'application Medix (définie dans config.php et les controllers)
    'session_key' => 'user_id',
    // Redirection si non connecté → page login de Medix
    'login_url'   => '/../../index.php?action=login',
    // Liens de navigation additionnels dans la barre du module historique
    'nav'         => [
        ['← Retour à Medix', '/../../index.php?action=dashboard'],
    ],
];
