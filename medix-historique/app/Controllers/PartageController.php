<?php
namespace App\Controllers;

use App\Core\{Controller, Session};
use App\Models\Partage;

final class PartageController extends Controller
{
    public function index(): void
    {
        $uid = $this->userId();
        $this->render('partage', [
            'titre' => 'Partage avec mon médecin',
            'sousTitre' => 'Choisissez ce que votre médecin peut voir',
            'retour' => ['label' => 'Tout mon historique', 'url' => '/'],
            'cats' => Partage::CATEGORIES,
            'etat' => Partage::lire($uid),
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierCsrf();
        $uid = $this->userId();
        Partage::enregistrer($uid, is_array($_POST['cat'] ?? null) ? $_POST['cat'] : []);
        Session::flash('ok', 'Préférences de partage enregistrées.');
        $this->redirect('/partage');
    }
}
