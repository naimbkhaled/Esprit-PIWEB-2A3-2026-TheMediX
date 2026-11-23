<?php
/**
* config/database.php
* Secure PDO database connection
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
'mysql:host=localhost;dbname=bibli', // set your database name here
'root',
'', // set your MySQL password here if needed
[
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]
);
} catch (Exception $e) {
die('Erreur: ' . $e->getMessage());
}
}
return self::$pdo;
}
}