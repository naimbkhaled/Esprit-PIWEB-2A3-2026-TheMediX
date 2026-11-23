<?php
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">'; }
function url(string $page, array $p = []): string { return 'index.php?' . http_build_query(['page' => $page] + $p); }
function statut_badge(string $s): string
{
    $m = ['en_attente' => ['en attente', 'bg-warning text-dark'], 'approuve' => ['approuvé', 'bg-success'], 'rejete' => ['rejeté', 'bg-danger']];
    [$l, $c] = $m[$s] ?? [$s, 'bg-secondary'];
    return '<span class="badge ' . $c . '">' . e($l) . '</span>';
}
function statut_options(string $sel): string
{
    $o = ['' => 'Tous les statuts', 'approuve' => 'approuvé', 'en_attente' => 'en attente', 'rejete' => 'rejeté'];
    $h = '';
    foreach ($o as $v => $l) { $h .= '<option value="' . $v . '"' . ($v === $sel ? ' selected' : '') . '>' . $l . '</option>'; }
    return $h;
}
function champ(string $id, string $label, string $kind, array $old, array $errors): string
{
    $inv = isset($errors[$id]) ? ' is-invalid' : '';
    $v = e($old[$id] ?? '');
    if ($kind === 'area') { $ctl = "<textarea id=\"$id\" name=\"$id\" rows=\"3\" class=\"form-control$inv\">$v</textarea>"; }
    elseif ($kind === 'password') { $ctl = "<input id=\"$id\" name=\"$id\" type=\"password\" class=\"form-control$inv\">"; }
    else { $ctl = "<input id=\"$id\" name=\"$id\" type=\"text\" class=\"form-control$inv\" value=\"$v\">"; }
    return "<div class=\"mb-3\"><label class=\"form-label\" for=\"$id\">$label</label>$ctl<div class=\"invalid-feedback\" id=\"err-$id\">" . e($errors[$id] ?? '') . '</div></div>';
}
function pagination(int $page, int $pages, string $nom, array $params): string
{
    if ($pages <= 1) { return ''; }
    $li = function ($n, $label, $dis = false, $act = false) use ($nom, $params) {
        return '<li class="page-item' . ($dis ? ' disabled' : '') . ($act ? ' active' : '') . '"><a class="page-link" href="' . e(url($nom, $params + ['p' => $n])) . '">' . $label . '</a></li>';
    };
    $h = '<nav><ul class="pagination justify-content-center mb-0">' . $li($page - 1, 'Précédent', $page <= 1);
    for ($i = 1; $i <= $pages; $i++) { $h .= $li($i, $i, false, $i === $page); }
    return $h . $li($page + 1, 'Suivant', $page >= $pages) . '</ul></nav>';
}
