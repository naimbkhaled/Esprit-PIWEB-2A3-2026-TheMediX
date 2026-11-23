<?php
// CONTROLLER de base : rendu des vues, requêtes PDO préparées, outils communs
abstract class Controller
{
    protected PDO $db;
    protected const STATUTS = ['en_attente', 'approuve', 'rejete'];

    public function __construct() { $this->db = getConnexion(); }

    protected function render(string $view, array $data = [], string $layout = 'front', string $title = 'HealthConnect', string $currentPage = ''): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/' . $layout . '.php';
    }
    protected function redirect(string $page, array $params = []): void { header('Location: ' . url($page, $params)); exit; }
    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
    protected function flash(string $type, string $msg): void { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }
    protected function csrfCheck(): void
    {
        if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) { http_response_code(403); die('Jeton CSRF invalide.'); }
    }
    protected function run(string $sql, array $p = []): PDOStatement
    {
        $st = $this->db->prepare($sql);
        foreach ($p as $k => $v) { $st->bindValue(':' . $k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR); }
        $st->execute();
        return $st;
    }
    protected function hydrateGroupe(array $r): Groupe
    {
        return new Groupe((int)$r['id'], $r['nom'], $r['description'], $r['regles'], $r['statut'], $r['motif_rejet'], $r['date_creation'],
            (int)$r['id_createur'], $r['nom_createur'] ?? '', (int)($r['nb_membres'] ?? 0), (bool)($r['est_membre'] ?? false));
    }
    protected function filtres(string $q, string $statut, string $w = '1=1', array $p = []): array
    {
        if ($q !== '') { $w .= ' AND g.nom LIKE :q'; $p['q'] = '%' . $q . '%'; }
        if (in_array($statut, self::STATUTS, true)) { $w .= ' AND g.statut = :s'; $p['s'] = $statut; }
        return [$w, $p];
    }
    protected function compter(string $w, array $p): int { return (int)$this->run("SELECT COUNT(*) FROM groupe g WHERE $w", $p)->fetchColumn(); }
    protected function listerGroupes(string $w, array $p, ?int $limit = null, int $offset = 0, int $uid = 0): array
    {
        $sql = "SELECT g.*, CONCAT(u.prenom, ' ', u.nom) AS nom_createur,
                  (SELECT COUNT(*) FROM membre_groupe m WHERE m.id_groupe = g.id) AS nb_membres,
                  (SELECT COUNT(*) FROM membre_groupe m2 WHERE m2.id_groupe = g.id AND m2.id_user = :uid) AS est_membre
                FROM groupe g JOIN user u ON u.id = g.id_createur WHERE $w ORDER BY g.date_creation DESC, g.id DESC";
        $p['uid'] = $uid;
        if ($limit !== null) { $sql .= ' LIMIT :lim OFFSET :off'; $p['lim'] = $limit; $p['off'] = $offset; }
        return array_map([$this, 'hydrateGroupe'], $this->run($sql, $p)->fetchAll());
    }
    protected function trouverGroupe(int $id, int $uid = 0): ?Groupe { return $this->listerGroupes('g.id = :gid', ['gid' => $id], null, 0, $uid)[0] ?? null; }
    protected function membres(int $idGroupe): array
    {
        return $this->run("SELECT CONCAT(u.prenom, ' ', u.nom) AS nom FROM membre_groupe m JOIN user u ON u.id = m.id_user WHERE m.id_groupe = :g ORDER BY m.date_adhesion", ['g' => $idGroupe])->fetchAll(PDO::FETCH_COLUMN);
    }
    protected function exporterCsv(array $groupes): void // ouvrable dans Excel
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="groupes.csv"');
        $f = fopen('php://output', 'w');
        fwrite($f, "\xEF\xBB\xBF");
        fputcsv($f, ['ID', 'Nom', 'Description', 'Règles', 'Statut', 'Créateur', 'Membres', 'Date de création'], ';', '"', '');
        foreach ($groupes as $g) {
            fputcsv($f, [$g->getId(), $g->getNom(), $g->getDescription(), $g->getRegles(), $g->getStatut(), $g->getNomCreateur(), $g->getNbMembres(), $g->getDateCreation()], ';', '"', '');
        }
        fclose($f);
        exit;
    }
}
