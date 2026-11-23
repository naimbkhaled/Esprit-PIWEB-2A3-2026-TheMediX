<?php
// Fonctions utilitaires pour les vues.

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function base_path(): string { return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); }
function url(string $path = '/'): string { return base_path() . $path; }
function asset(string $path): string { return base_path() . '/assets/' . ltrim($path, '/'); }

function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(\App\Core\Session::csrf()) . '">'; }

function date_fr(?string $d): string
{
    if (!$d) return '–';
    $t = strtotime($d);
    return strlen($d) > 10 ? date('d/m/Y H:i', $t) : date('d/m/Y', $t);
}

/** Texte affiché pour une valeur de champ (liste et export). */
function fmt_val(array $c, $v, array $opts = []): string
{
    if ($v === null || $v === '') return '–';
    switch ($c['type']) {
        case 'datetime':
        case 'date':     return date_fr((string)$v);
        case 'bool':     return $v ? 'Oui' : 'Non';
        case 'select':   return (string)($opts[$v] ?? $v);
        case 'number':   return (string)($v + 0);
        case 'textarea': return mb_strimwidth((string)$v, 0, 140, '…');
        default:         return (string)$v;
    }
}

/** Champ de formulaire (label + contrôle) selon la description du champ. */
function champ_html(array $c, $val, array $opts = []): string
{
    $n = e($c['nom']); $id = 'f_' . $n; $lab = e($c['label']); $t = $c['type'];
    $req = !empty($c['req']) ? ' required' : '';

    if ($t === 'bool') {
        return '<div class="full check"><input type="checkbox" id="' . $id . '" name="' . $n . '" value="1"' . (!empty($val) ? ' checked' : '') . $req
             . '><label for="' . $id . '" style="margin:0">' . $lab . '</label></div>';
    }
    $h = '<div' . ($t === 'textarea' ? ' class="full"' : '') . '><label for="' . $id . '">' . $lab . '</label>';
    if ($t === 'textarea') {
        $h .= '<textarea id="' . $id . '" name="' . $n . '" rows="3" maxlength="2000"' . $req . '>' . e($val) . '</textarea>';
    } elseif ($t === 'select') {
        $h .= '<select id="' . $id . '" name="' . $n . '"' . $req . '>';
        if (empty($c['req'])) $h .= '<option value="">—</option>';
        foreach ($opts as $k => $l) $h .= '<option value="' . e($k) . '"' . ((string)$val === (string)$k ? ' selected' : '') . '>' . e($l) . '</option>';
        $h .= '</select>';
    } else {
        $type = ['datetime' => 'datetime-local', 'date' => 'date', 'number' => 'number'][$t] ?? 'text';
        $v = $val;
        if ($t === 'datetime' && $val !== '' && $val !== null) $v = date('Y-m-d\TH:i', strtotime((string)$val));
        elseif ($t === 'date' && $val !== '' && $val !== null) $v = date('Y-m-d', strtotime((string)$val));
        elseif ($t === 'number' && $val !== '' && $val !== null) $v = $val + 0;
        $extra = $t === 'number' ? ' step="' . e($c['step'] ?? '1') . '" min="' . e($c['min'] ?? 0) . '" max="' . e($c['max'] ?? 1000000) . '"' : '';
        $h .= '<input id="' . $id . '" name="' . $n . '" type="' . $type . '" value="' . e($v) . '"' . $extra . $req . '>';
    }
    return $h . '</div>';
}

/** Neutralise l'injection de formules dans Excel (cellules commençant par = + - @). */
function csv_safe($v): string
{
    $s = (string)$v;
    return ($s !== '' && strpos('=+-@', $s[0]) !== false) ? "'" . $s : $s;
}
