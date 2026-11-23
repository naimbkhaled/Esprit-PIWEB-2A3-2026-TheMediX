<?php
namespace App\Core;

/** Valide et convertit les champs envoyés par un formulaire selon la description du module. */
final class Validator
{
    /** @return array{0: array, 1: ?string} [valeurs, message d'erreur] */
    public static function champs(array $mod, array $in, callable $options): array
    {
        $vals = [];
        foreach ($mod['champs'] as $c) {
            if (($c['form'] ?? true) === false) continue;
            $n = $c['nom']; $l = $c['label']; $req = !empty($c['req']);
            $raw = $in[$n] ?? '';
            if (is_array($raw)) $raw = '';
            if (is_string($raw)) $raw = trim($raw);
            $oblig = [[], "« $l » est obligatoire."];

            switch ($c['type']) {
                case 'bool':
                    $v = empty($in[$n]) ? 0 : 1;
                    if ($req && !$v) return $oblig;
                    break;
                case 'date':
                case 'datetime':
                    if ($raw === '') { if ($req) return $oblig; $v = null; break; }
                    $t = strtotime(str_replace('T', ' ', $raw));
                    if ($t === false) return [[], "« $l » : date invalide."];
                    if (empty($c['futur_ok']) && $t > time() + 86400) return [[], "« $l » ne peut pas être dans le futur."];
                    $v = date($c['type'] === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s', $t);
                    break;
                case 'number':
                    $s = str_replace(',', '.', (string)$raw);
                    if ($s === '') { if ($req) return $oblig; $v = null; break; }
                    if (!is_numeric($s)) return [[], "« $l » : nombre invalide."];
                    $f = (float)$s;
                    if ($f < ($c['min'] ?? 0) || $f > ($c['max'] ?? 1000000)) return [[], "« $l » : valeur hors limites."];
                    $v = $f;
                    break;
                case 'select':
                    if ($raw === '') { if ($req) return $oblig; $v = null; break; }
                    if (!array_key_exists($raw, $options($c))) return [[], "« $l » : choix invalide."];
                    $v = $raw;
                    break;
                default: // text, textarea
                    if ($raw === '') { if ($req) return $oblig; $v = null; break; }
                    if (!empty($c['regex']) && !preg_match($c['regex'], $raw)) return [[], "« $l » : format invalide."];
                    $v = mb_substr($raw, 0, $c['max_len'] ?? ($c['type'] === 'textarea' ? 2000 : 255));
            }
            $vals[$n] = $v;
        }
        return [$vals, null];
    }
}
