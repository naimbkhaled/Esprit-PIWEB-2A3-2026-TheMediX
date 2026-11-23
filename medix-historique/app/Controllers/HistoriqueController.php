<?php
namespace App\Controllers;

use App\Core\{Config, Controller, Session, Validator};
use App\Models\Historique;

final class HistoriqueController extends Controller
{
    /** Page d'accueil : tous les historiques, regroupés. */
    public function hub(): void
    {
        $uid = $this->userId();
        $groupes = [];
        foreach (Config::modules() as $slug => $m) {
            $m['slug'] = $slug;
            $m['stats'] = Historique::stats($m, $uid);
            $groupes[$m['groupe']][] = $m;
        }
        $this->render('hub', ['titre' => 'Mon historique', 'groupes' => $groupes]);
    }

    /** Liste d'un historique, avec filtres et formulaire d'ajout / modification. */
    public function liste(string $slug): void
    {
        $uid = $this->userId();
        $mod = $this->module($slug);
        $f = ['du' => $this->dateGet('du'), 'au' => $this->dateGet('au'), 'q' => trim((string)($_GET['q'] ?? ''))];
        $edit = null;
        if (isset($_GET['edit']) && in_array('edit', $mod['perm'], true)) $edit = Historique::trouver($mod, $uid, (int)$_GET['edit']);

        $this->render('liste', [
            'titre' => $mod['titre'], 'slug' => $slug, 'mod' => $mod, 'f' => $f, 'edit' => $edit,
            'rows' => Historique::lister($mod, $uid, $f),
            'opts' => Historique::optionsModule($mod, $uid),
        ]);
    }

    public function enregistrer(string $slug): void
    {
        $this->verifierCsrf();
        $uid = $this->userId();
        $mod = $this->module($slug);
        $id = (int)($_POST['id'] ?? 0);
        if (!in_array($id ? 'edit' : 'add', $mod['perm'], true)) $this->abort(403, 'Action non autorisée.');

        [$vals, $err] = Validator::champs($mod, $_POST, fn($c) => Historique::options($c, $uid));
        if (!$err && isset($mod['valider'])) $err = ($mod['valider'])($vals);
        if ($err) {
            Session::flash('erreur', $err);
            $this->redirect('/h/' . $slug . ($id ? '?edit=' . $id : ''));
        }
        if ($id) Historique::modifier($mod, $uid, $id, $vals); else Historique::inserer($mod, $uid, $vals);
        Session::flash('ok', 'Enregistré.');
        $this->redirect('/h/' . $slug);
    }

    public function supprimer(string $slug, string $id): void
    {
        $this->verifierCsrf();
        $uid = $this->userId();
        $mod = $this->module($slug);
        if (!in_array('delete', $mod['perm'], true)) $this->abort(403, 'Action non autorisée.');
        Historique::supprimer($mod, $uid, (int)$id);
        Session::flash('ok', 'Entrée supprimée.');
        $this->redirect('/h/' . $slug);
    }

    public function effacerTout(string $slug): void
    {
        $this->verifierCsrf();
        $uid = $this->userId();
        $mod = $this->module($slug);
        if (empty($mod['effacer_tout']) || !in_array('delete', $mod['perm'], true)) $this->abort(403, 'Action non autorisée.');
        Historique::toutEffacer($mod, $uid);
        Session::flash('ok', 'Historique effacé.');
        $this->redirect('/h/' . $slug);
    }
}
