<?php
// CONTROLLER patient : logique métier de la gestion des groupes (front office)
class GroupeController extends Controller
{
    private int $uid;

    public function __construct()
    {
        parent::__construct();
        if (empty($_SESSION['patient_id'])) { // SIMULATION en attendant le module de connexion de l'équipe
            try {
                $id = $this->run("SELECT id FROM user WHERE role = 'patient' ORDER BY id LIMIT 1")->fetchColumn();
                $_SESSION['patient_id'] = $id ? (int)$id : 2;
            } catch (Exception $e) {
                $_SESSION['patient_id'] = 2;
            }
        }
        $this->uid = (int)$_SESSION['patient_id'];
    }
    private function visibles(bool $mine, string $q, string $statut): array
    {
        $w = "(g.statut = 'approuve' OR g.id_createur = :u1)"; $p = ['u1' => $this->uid];
        if ($mine) { $w .= ' AND g.id_createur = :u2'; $p['u2'] = $this->uid; }
        return $this->filtres($q, $statut, $w, $p);
    }
    private function valider(array $d): array
    {
        $e = [];
        $regles = ['nom' => [3, 50, 'Le nom'], 'description' => [10, 300, 'La description'], 'regles' => [10, 500, 'Les règles']];
        foreach ($regles as $k => [$min, $max, $l]) {
            $n = mb_strlen($d[$k]);
            if ($n === 0) { $e[$k] = "$l est obligatoire."; }
            elseif ($n < $min) { $e[$k] = "$l : minimum $min caractères."; }
            elseif ($n > $max) { $e[$k] = "$l : maximum $max caractères."; }
        }
        return $e;
    }
    public function home(): void { $this->render('front/home', [], 'plain', 'Accueil'); }

    public function index(): void
    {
        $mine = ($_GET['onglet'] ?? '') === 'mes'; $q = trim($_GET['q'] ?? ''); $statut = $_GET['statut'] ?? '';
        [$w, $p] = $this->visibles($mine, $q, $statut);
        $pages = max(1, (int)ceil($this->compter($w, $p) / PER_PAGE));
        $page = min($pages, max(1, (int)($_GET['p'] ?? 1)));
        $groupes = $this->listerGroupes($w, $p, PER_PAGE, ($page - 1) * PER_PAGE, $this->uid);
        $this->render('front/groupes', compact('groupes', 'mine', 'q', 'statut', 'page', 'pages'), 'front', 'Groupes', 'groupes');
    }
    public function export(): void
    {
        [$w, $p] = $this->visibles(($_GET['onglet'] ?? '') === 'mes', trim($_GET['q'] ?? ''), $_GET['statut'] ?? '');
        $this->exporterCsv($this->listerGroupes($w, $p, null, 0, $this->uid));
    }
    public function form(): void
    {
        $id = (int)($_GET['id'] ?? 0); $old = []; $g = null;
        if ($id) {
            $g = $this->trouverGroupe($id, $this->uid);
            if (!$g || $g->getIdCreateur() !== $this->uid) { $this->flash('danger', 'Groupe introuvable ou accès refusé.'); $this->redirect('groupes'); }
            $old = ['nom' => $g->getNom(), 'description' => $g->getDescription(), 'regles' => $g->getRegles()];
        }
        $this->render('front/groupe-form', ['id' => $id, 'old' => $old, 'errors' => []], 'front', 'Groupe', 'groupe-form');
    }
    public function save(): void
    {
        if (!$this->isPost()) { $this->redirect('groupes'); }
        $this->csrfCheck();
        $id = (int)($_POST['id'] ?? 0);
        $d = ['nom' => trim($_POST['nom'] ?? ''), 'description' => trim($_POST['description'] ?? ''), 'regles' => trim($_POST['regles'] ?? '')];
        if ($id) {
            $g = $this->trouverGroupe($id, $this->uid);
            if (!$g || $g->getIdCreateur() !== $this->uid) { $this->flash('danger', 'Accès refusé.'); $this->redirect('groupes'); }
        }
        $errors = $this->valider($d);
        if ($errors) { $this->render('front/groupe-form', ['id' => $id, 'old' => $d, 'errors' => $errors], 'front', 'Groupe', 'groupe-form'); return; }
        $p = ['n' => $d['nom'], 'd' => $d['description'], 'r' => $d['regles']];
        if ($id) { // un groupe rejeté repasse en attente après modification
            $this->run("UPDATE groupe SET nom = :n, description = :d, regles = :r, statut = IF(statut = 'rejete', 'en_attente', statut) WHERE id = :id AND id_createur = :u", $p + ['id' => $id, 'u' => $this->uid]);
            $this->flash('success', 'Groupe modifié.');
        } else {
            $this->run("INSERT INTO groupe (nom, description, regles, statut, id_createur) VALUES (:n, :d, :r, 'en_attente', :u)", $p + ['u' => $this->uid]);
            $this->run('INSERT INTO membre_groupe (id_user, id_groupe) VALUES (:u, :g)', ['u' => $this->uid, 'g' => (int)$this->db->lastInsertId()]);
            $this->flash('success', 'Groupe créé : il est en attente d’approbation par l’administrateur.');
        }
        $this->redirect('groupes');
    }
    public function delete(): void
    {
        if (!$this->isPost()) { $this->redirect('groupes'); }
        $this->csrfCheck();
        $n = $this->run('DELETE FROM groupe WHERE id = :id AND id_createur = :u', ['id' => (int)($_POST['id'] ?? 0), 'u' => $this->uid])->rowCount();
        $this->flash($n ? 'success' : 'danger', $n ? 'Groupe supprimé.' : 'Suppression impossible.');
        $this->redirect('groupes');
    }
    public function join(): void
    {
        if (!$this->isPost()) { $this->redirect('groupes'); }
        $this->csrfCheck();
        $id = (int)($_POST['id'] ?? 0);
        $n = $this->run("INSERT IGNORE INTO membre_groupe (id_user, id_groupe) SELECT :u, g.id FROM groupe g WHERE g.id = :id AND g.statut = 'approuve'", ['u' => $this->uid, 'id' => $id])->rowCount();
        $this->flash($n ? 'success' : 'info', $n ? 'Vous avez rejoint le groupe.' : 'Adhésion impossible (déjà membre ou groupe non approuvé).');
        $this->redirect('groupe-detail', ['id' => $id]);
    }
    public function detail(): void
    {
        $g = $this->trouverGroupe((int)($_GET['id'] ?? 0), $this->uid);
        if (!$g || ($g->getStatut() !== 'approuve' && $g->getIdCreateur() !== $this->uid)) { $this->flash('danger', 'Groupe introuvable.'); $this->redirect('groupes'); }
        $this->render('front/groupe-detail', ['groupe' => $g, 'membres' => $this->membres($g->getId())], 'front', 'Détails du groupe', 'groupes');
    }
    public function invite(): void
    {
        if (!$this->isPost()) { $this->redirect('groupes'); }
        $this->csrfCheck();
        $id = (int)($_POST['id'] ?? 0); $v = trim($_POST['invite'] ?? '');
        $g = $this->trouverGroupe($id, $this->uid);
        if (!$g || !$g->getEstMembre() || $g->getStatut() !== 'approuve') { $this->flash('danger', 'Invitation impossible.'); $this->redirect('groupes'); }
        if (mb_strlen($v) < 3 || (strpos($v, '@') !== false && !filter_var($v, FILTER_VALIDATE_EMAIL))) { $this->flash('danger', 'Saisissez un email valide ou un identifiant d’au moins 3 caractères.'); $this->redirect('groupe-detail', ['id' => $id]); }
        $inv = $this->run("SELECT id FROM user WHERE role = 'patient' AND (email = :a OR identifiant = :b)", ['a' => $v, 'b' => $v])->fetchColumn();
        if (!$inv) { $this->flash('danger', 'Aucun patient trouvé avec cet email ou identifiant.'); }
        elseif ($this->run('SELECT COUNT(*) FROM membre_groupe WHERE id_user = :u AND id_groupe = :g', ['u' => (int)$inv, 'g' => $id])->fetchColumn()) { $this->flash('info', 'Ce patient est déjà membre.'); }
        else {
            $n = $this->run('INSERT IGNORE INTO invitation (id_groupe, id_invite, id_inviteur) VALUES (:g, :i, :u)', ['g' => $id, 'i' => (int)$inv, 'u' => $this->uid])->rowCount();
            $this->flash($n ? 'success' : 'info', $n ? 'Invitation envoyée.' : 'Ce patient a déjà été invité.');
        }
        $this->redirect('groupe-detail', ['id' => $id]);
    }
}
