<?php
/**
 * config/database.php
 * Secure PDO database connection (Singleton)
 */
class Database
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    // --- Adjust these to match your local MySQL / phpMyAdmin setup ---
                    'mysql:host=localhost;dbname=session;charset=utf8mb4', // your database name
                    'root',
                    '', // your MySQL password if needed
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
