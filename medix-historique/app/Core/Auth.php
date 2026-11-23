<?php
namespace App\Core;

/** Reads the current user from the shared Medix session and users table. */
final class Auth
{
    public static function id(): ?int
    {
        $k = Config::get('session_key', 'uid');
        return isset($_SESSION[$k]) ? (int)$_SESSION[$k] : null;
    }

    public static function user(): ?array
    {
        $id = self::id();
        if ($id === null) return null;
        $s = Database::pdo()->prepare('SELECT id, username AS nom, email FROM users WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function deconnecter(): void
    {
        unset($_SESSION[Config::get('session_key', 'uid')]);
    }
}
