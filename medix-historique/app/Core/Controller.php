<?php
namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
        exit;
    }

    protected function abort(int $code, string $message): never
    {
        View::render('erreur', ['titre' => 'Erreur', 'message' => $message], $code);
        exit;
    }

    /** Identifiant de l'utilisateur connecté, sinon redirection vers la connexion. */
    protected function userId(): int
    {
        $id = Auth::id();
        if ($id === null) $this->redirect(Config::get('login_url', '/dev/connexion'));
        return $id;
    }

    protected function verifierCsrf(): void
    {
        if (!Session::csrfValide((string)($_POST['csrf'] ?? ''))) $this->abort(400, 'Requête invalide (CSRF).');
    }

    /** Retourne la configuration d'un module d'historique (ou 404). */
    protected function module(string $slug, bool $generique = true): array
    {
        $m = Config::modules()[$slug] ?? null;
        if ($m === null || ($generique && isset($m['special']))) $this->abort(404, 'Historique introuvable.');
        return $m;
    }

    protected function dateGet(string $cle): string
    {
        $v = (string)($_GET[$cle] ?? '');
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
    }
}
