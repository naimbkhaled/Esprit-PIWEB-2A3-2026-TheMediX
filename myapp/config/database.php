<?php
// Bridge: load the root config.php (class Config) and expose
// Database::getConnexion() so the rest of the project is untouched.
require_once __DIR__ . '/../config.php';

class Database
{
    public static function getConnexion(): PDO
    {
        return Config::getConnexion();
    }
}
