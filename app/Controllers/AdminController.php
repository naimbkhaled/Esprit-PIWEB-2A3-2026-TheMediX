<?php
// CONTROLLER admin : logique métier du back office (session + rôle admin obligatoires)
class AdminController extends Controller
{
    private function requireAdmin(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin' || empty($_SESSION['admin_id'])) { $this->redirect('admin-login'); }
    }
    public function login(): void
    {
        if (!empty($_SESSION['admin_id'])) { $this->redirect('admin-groupes'); }
        $errors = []; $old = [];
        if ($this->isPost()) {
            $this->csrfCheck();
            $id = trim($_POST['login'] ?? ''); $pw = $_POST['mdp'] ?? ''; $old = ['login' => $id];
            if (mb_strlen($id) < 3) { $errors['login'] = 'L’identifiant : minimum 3 caractères.'; }
            if (strlen($pw) < 6) { $errors['mdp'] = 'Mot de passe : minimum 6 caractères.'; }
            if (!$errors) {
                $u = $this->run("SELECT id, mot_de_passe FROM user WHERE identifiant = :i AND role = 'admin'", ['i' => $id])->fetch();
                if ($u && password_verify($pw, $u['mot_de_passe'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = (int)$u['id']; $_SESSION['role'] = 'admin';
                    $this->redirect('admin-groupes');
                }
                $errors['mdp'] = 'Identifiant ou mot de passe incorrect.';
            }
        }
        $this->render('back/admin-login', compact('errors', 'old'), 'plain', 'Connexion administrateur');
    }
    public function logout(): void { unset($_SESSION['admin_id'], $_SESSION['role']); session_regenerate_id(true); $this->redirect('home'); }

    public function index(): void
    {
        $this->requireAdmin();
        $q = trim($_GET['q'] ?? ''); $statut = $_GET['statut'] ?? '';
        [$w, $p] = $this->filtres($q, $statut);
        $pages = max(1, (int)ceil($this->compter($w, $p) / PER_PAGE));
        $page = min($pages, max(1, (int)($_GET['p'] ?? 1)));
        $groupes = $this->listerGroupes($w, $p, PER_PAGE, ($page - 1) * PER_PAGE);
        $this->render('back/admin-groupes', compact('groupes', 'q', 'statut', 'page', 'pages'), 'back', 'Gestion des groupes', 'admin-groupes');
    }
    public function export(): void
    {
        $this->requireAdmin();
        [$w, $p] = $this->filtres(trim($_GET['q'] ?? ''), $_GET['statut'] ?? '');
        $this->exporterCsv($this->listerGroupes($w, $p));
    }
    public function detail(): void
    {
        $this->requireAdmin();
        $g = $this->trouverGroupe((int)($_GET['id'] ?? 0));
        if (!$g) { $this->flash('danger', 'Groupe introuvable.'); $this->redirect('admin-groupes'); }
        $this->render('back/admin-groupe-detail', ['groupe' => $g, 'membres' => $this->membres($g->getId())], 'back', 'Détails du groupe', 'admin-groupes');
    }
    public function approve(): void
    {
        $this->requireAdmin();
        if ($this->isPost()) {
            $this->csrfCheck();
            $this->run("UPDATE groupe SET statut = 'approuve', motif_rejet = NULL WHERE id = :id", ['id' => (int)($_POST['id'] ?? 0)]);
            $this->flash('success', 'Groupe approuvé.');
        }
        $this->redirect('admin-groupes');
    }
    public function reject(): void
    {
        $this->requireAdmin();
        if ($this->isPost()) {
            $this->csrfCheck();
            $motif = trim($_POST['motif'] ?? '');
            if (mb_strlen($motif) < 5 || mb_strlen($motif) > 200) { $this->flash('danger', 'Le motif doit contenir entre 5 et 200 caractères.'); }
            else {
                $this->run("UPDATE groupe SET statut = 'rejete', motif_rejet = :m WHERE id = :id", ['m' => $motif, 'id' => (int)($_POST['id'] ?? 0)]);
                $this->flash('success', 'Groupe rejeté.');
            }
        }
        $this->redirect('admin-groupes');
    }
}
