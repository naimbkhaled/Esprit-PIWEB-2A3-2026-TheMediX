<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Historique;

final class ExportController extends Controller
{
    /** Export CSV d'un historique (mêmes filtres que la liste), lisible dans Excel. */
    public function csv(string $slug): void
    {
        $uid = $this->userId();
        $mod = $this->module($slug);
        $f = ['du' => $this->dateGet('du'), 'au' => $this->dateGet('au'), 'q' => trim((string)($_GET['q'] ?? ''))];
        $rows = Historique::lister($mod, $uid, $f, 5000);
        $opts = Historique::optionsModule($mod, $uid);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="medix-' . $slug . '-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");                                     // BOM : accents corrects dans Excel
        fputcsv($out, array_map('csv_safe', array_column($mod['champs'], 'label')), ';');
        foreach ($rows as $r) {
            fputcsv($out, array_map(fn($c) => csv_safe(fmt_val($c, $r[$c['nom']] ?? null, $opts[$c['nom']] ?? [])), $mod['champs']), ';');
        }
    }

    /** Export JSON de toutes les données de l'utilisateur. */
    public function json(): void
    {
        $uid = $this->userId();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="medix-mes-donnees-' . date('Y-m-d') . '.json"');
        echo json_encode(['export' => date('c'), 'donnees' => Historique::toutExporter($uid)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
