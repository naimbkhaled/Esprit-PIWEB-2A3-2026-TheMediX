<?php
namespace App\Core;

/**
 * POINT D'INTÉGRATION UNIQUE avec le reste de l'application.
 * Le module n'a besoin que d'une chose : l'identifiant de l'utilisateur connecté.
 * Aujourd'hui il le lit dans $_SESSION[session_key]. À l'intégration, remplacez le corps
 * de id() par l'appel au système de comptes de l'application globale.
 */
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
        $s = Database::pdo()->prepare('SELECT id, nom, email FROM utilisateurs WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function deconnecter(): void
    {
        unset($_SESSION[Config::get('session_key', 'uid')]);
    }
}
