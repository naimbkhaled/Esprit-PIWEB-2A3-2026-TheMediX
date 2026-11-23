<?php
namespace App\Models;

use App\Core\Config;
use App\Core\Database;
use PDO;

/**
 * Accès aux données de tous les historiques, piloté par app/config/modules.php.
 * Les noms de tables et de colonnes viennent uniquement de la configuration (jamais de l'utilisateur) ;
 * toutes les valeurs passent par des requêtes préparées ; chaque requête est limitée à l'utilisateur.
 */
final class Historique
{
    private static function q(string $c): string { return '`' . str_replace('`', '', $c) . '`'; }
    private static function cle(array $m): string { return $m['cle'] ?? 'id'; }
    private static function from(array $m): string { return $m['from'] ?? self::q($m['table']) . ' t'; }

    private static function dateCol(array $m): ?string
    {
        $d = $m['date'] ?? null;
        return $d ? (str_contains($d, '.') ? $d : 't.' . self::q($d)) : null;
    }

    private static function select(array $m): string
    {
        if (isset($m['select'])) return $m['select'];
        $p = ['t.' . self::q(self::cle($m)) . ' AS id'];
        foreach ($m['champs'] as $c) $p[] = 't.' . self::q($c['nom']);
        return implode(', ', $p);
    }

    private static function champsTexte(array $m): array
    {
        if (($m['recherche'] ?? true) === false) return [];
        return array_column(array_filter($m['champs'], fn($c) => in_array($c['type'], ['text', 'textarea'], true)), 'nom');
    }

    public static function rechercheActive(array $m): bool { return self::champsTexte($m) !== []; }

    /** @param array{du?:string,au?:string,q?:string} $f */
    public static function lister(array $m, int $uid, array $f = [], int $limite = 500): array
    {
        $sql = 'SELECT ' . self::select($m) . ' FROM ' . self::from($m) . ' WHERE t.`utilisateur_id` = :u';
        $p = [':u' => $uid];
        $d = self::dateCol($m);
        if ($d && !empty($f['du'])) { $sql .= " AND $d >= :du"; $p[':du'] = $f['du'] . ' 00:00:00'; }
        if ($d && !empty($f['au'])) { $sql .= " AND $d <= :au"; $p[':au'] = $f['au'] . ' 23:59:59'; }
        if (!empty($f['q'])) {
            $c = [];
            foreach (self::champsTexte($m) as $i => $n) { $c[] = 't.' . self::q($n) . " LIKE :q$i"; $p[":q$i"] = '%' . $f['q'] . '%'; }
            if ($c) $sql .= ' AND (' . implode(' OR ', $c) . ')';
        }
        $sql .= ' ORDER BY ' . ($d ?? 't.' . self::q(self::cle($m))) . ' DESC LIMIT ' . (int)$limite;
        $s = Database::pdo()->prepare($sql);
        $s->execute($p);
        return $s->fetchAll();
    }

    public static function trouver(array $m, int $uid, int $id): ?array
    {
        $s = Database::pdo()->prepare('SELECT t.* FROM ' . self::q($m['table']) . ' t WHERE t.`utilisateur_id` = ? AND t.' . self::q(self::cle($m)) . ' = ?');
        $s->execute([$uid, $id]);
        return $s->fetch() ?: null;
    }

    public static function inserer(array $m, int $uid, array $vals): void
    {
        $cols = array_map([self::class, 'q'], array_keys($vals));
        $s = Database::pdo()->prepare('INSERT INTO ' . self::q($m['table']) . ' (`utilisateur_id`, ' . implode(', ', $cols) . ') VALUES (?' . str_repeat(', ?', count($cols)) . ')');
        $s->execute([$uid, ...array_values($vals)]);
    }

    public static function modifier(array $m, int $uid, int $id, array $vals): void
    {
        $set = implode(', ', array_map(fn($c) => self::q($c) . ' = ?', array_keys($vals)));
        $s = Database::pdo()->prepare('UPDATE ' . self::q($m['table']) . " SET $set WHERE `utilisateur_id` = ? AND " . self::q(self::cle($m)) . ' = ?');
        $s->execute([...array_values($vals), $uid, $id]);
    }

    public static function supprimer(array $m, int $uid, int $id): void
    {
        $s = Database::pdo()->prepare('DELETE FROM ' . self::q($m['table']) . ' WHERE `utilisateur_id` = ? AND ' . self::q(self::cle($m)) . ' = ?');
        $s->execute([$uid, $id]);
    }

    public static function toutEffacer(array $m, int $uid): void
    {
        $s = Database::pdo()->prepare('DELETE FROM ' . self::q($m['table']) . ' WHERE `utilisateur_id` = ?');
        $s->execute([$uid]);
    }

    /** Nombre d'entrées et date de la dernière, pour le hub. */
    public static function stats(array $m, int $uid): array
    {
        $d = $m['date'] ?? null;
        $sql = 'SELECT COUNT(*) AS n, ' . ($d ? 'MAX(' . self::q($d) . ')' : 'NULL') . ' AS d FROM ' . self::q($m['table'])
             . ' WHERE `utilisateur_id` = ?' . (isset($m['where']) ? ' AND ' . $m['where'] : '');
        $s = Database::pdo()->prepare($sql);
        $s->execute([$uid]);
        return $s->fetch();
    }

    /** Choix possibles d'un champ « select » : liste fixe ou requête SQL (id => libellé). */
    public static function options(array $champ, int $uid): array
    {
        if (isset($champ['options'])) return $champ['options'];
        if (isset($champ['options_sql'])) {
            $s = Database::pdo()->prepare($champ['options_sql']);
            $s->execute(array_fill(0, substr_count($champ['options_sql'], '?'), $uid));
            return $s->fetchAll(PDO::FETCH_KEY_PAIR);
        }
        return [];
    }

    public static function optionsModule(array $m, int $uid): array
    {
        $o = [];
        foreach ($m['champs'] ?? [] as $c) if ($c['type'] === 'select') $o[$c['nom']] = self::options($c, $uid);
        return $o;
    }

    /** Toutes les données de l'utilisateur (export JSON, portabilité). */
    public static function toutExporter(int $uid): array
    {
        $out = [];
        foreach (Config::modules() as $m) {
            $t = $m['table'] ?? null;
            if (!$t || isset($out[$t])) continue;
            $s = Database::pdo()->prepare('SELECT * FROM ' . self::q($t) . ' WHERE `utilisateur_id` = ?');
            $s->execute([$uid]);
            $out[$t] = $s->fetchAll();
        }
        return $out;
    }
}
