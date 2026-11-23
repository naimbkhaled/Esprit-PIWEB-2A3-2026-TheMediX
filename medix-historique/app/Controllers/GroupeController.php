<?php
namespace App\Controllers;

use App\Core\{Controller, Session};
use App\Models\Groupe;

final class GroupeController extends Controller
{
    public function index(): void
    {
        $this->render('groupes', [
            'titre' => 'Mes groupes de soutien',
            'sousTitre' => 'Rejoignez ou quittez un groupe à tout moment',
            'retour' => ['label' => 'Tout mon historique', 'url' => '/'],
            'groupes' => Groupe::tous($this->userId()),
        ]);
    }

    public function rejoindre(): void
    {
        $this->verifierCsrf();
        Groupe::rejoindre($this->userId(), (int)($_POST['groupe_id'] ?? 0));
        Session::flash('ok', 'Vous avez rejoint le groupe.');
        $this->redirect('/groupes');
    }

    public function quitter(): void
    {
        $this->verifierCsrf();
        Groupe::quitter($this->userId(), (int)($_POST['groupe_id'] ?? 0));
        Session::flash('ok', 'Vous avez quitté le groupe.');
        $this->redirect('/groupes');
    }
}
