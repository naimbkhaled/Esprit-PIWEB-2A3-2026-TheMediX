<?php
namespace App\Controllers;

use App\Core\{Auth, Config, Controller, Database, Session};

/** Connexion de TEST uniquement (dev_mode = true). Disparaît à l'intégration. */
final class DevController extends Controller
{
    public function __construct()
    {
        if (!Config::get('dev_mode')) $this->abort(404, 'Page introuvable.');
    }

    public function form(): void
    {
        $users = Database::pdo()->query('SELECT id, nom, email FROM utilisateurs ORDER BY id')->fetchAll();
        $this->render('dev_connexion', ['titre' => 'Connexion de test', 'users' => $users]);
    }

    public function connecter(): void
    {
        $this->verifierCsrf();
        $pdo = Database::pdo();
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $nom = trim((string)($_POST['nom'] ?? '')) ?: 'Utilisateur test';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { Session::flash('erreur', 'E-mail invalide.'); $this->redirect('/dev/connexion'); }

        $s = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?'); $s->execute([$email]);
        $id = $s->fetchColumn();
        if (!$id) {
            $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe_hash) VALUES (?,?,?)')
                ->execute([$nom, $email, password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
            $id = $pdo->lastInsertId();
        }
        session_regenerate_id(true);
        $_SESSION[Config::get('session_key', 'uid')] = (int)$id;
        $pdo->prepare('INSERT INTO connexions (utilisateur_id, ip, appareil) VALUES (?,?,?)')
            ->execute([$id, $_SERVER['REMOTE_ADDR'] ?? '', mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 150)]);
        $this->redirect('/');
    }

    public function deconnecter(): void
    {
        Auth::deconnecter();
        $this->redirect('/dev/connexion');
    }
}
