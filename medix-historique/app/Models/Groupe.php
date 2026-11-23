<?php
namespace App\Models;

use App\Core\Database;

final class Groupe
{
    public static function tous(int $uid): array
    {
        $s = Database::pdo()->prepare('SELECT g.id, g.nom, g.description, (m.utilisateur_id IS NOT NULL) AS membre
            FROM groupes g LEFT JOIN groupes_membres m ON m.groupe_id = g.id AND m.utilisateur_id = ? ORDER BY g.nom');
        $s->execute([$uid]);
        return $s->fetchAll();
    }
    public static function rejoindre(int $uid, int $gid): void
    {
        Database::pdo()->prepare('INSERT IGNORE INTO groupes_membres (utilisateur_id, groupe_id) SELECT ?, id FROM groupes WHERE id = ?')->execute([$uid, $gid]);
    }
    public static function quitter(int $uid, int $gid): void
    {
        Database::pdo()->prepare('DELETE FROM groupes_membres WHERE utilisateur_id = ? AND groupe_id = ?')->execute([$uid, $gid]);
    }
}
