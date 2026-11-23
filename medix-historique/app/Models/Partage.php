<?php
namespace App\Models;

use App\Core\Database;
use PDO;

final class Partage
{
    public const CATEGORIES = [
        'glycemie' => 'Glycémie', 'a1c' => 'HbA1c', 'medicaments' => 'Médicaments',
        'alimentation' => 'Alimentation (repas)', 'activite' => 'Activité physique', 'insuline' => "Journal d'insuline",
    ];

    /** @return array<string,int> categorie => 0|1 (absent = non partagé) */
    public static function lire(int $uid): array
    {
        $s = Database::pdo()->prepare('SELECT categorie, autorise FROM partages WHERE utilisateur_id = ?');
        $s->execute([$uid]);
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function enregistrer(int $uid, array $coches): void
    {
        $s = Database::pdo()->prepare('INSERT INTO partages (utilisateur_id, categorie, autorise) VALUES (?,?,?) ON DUPLICATE KEY UPDATE autorise = VALUES(autorise)');
        foreach (array_keys(self::CATEGORIES) as $k) $s->execute([$uid, $k, empty($coches[$k]) ? 0 : 1]);
    }
}
