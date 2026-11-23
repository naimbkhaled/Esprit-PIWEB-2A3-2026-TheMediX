<?php
// CONTROLLER : logique métier (contrôle de saisie, ajout, modification, suppression, lecture)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Medecin.php';

class UserController
{
    private PDO $db;
    private const UPLOAD_DIR = __DIR__ . '/../uploads/';
    private const MAX_SIZE = 5 * 1024 * 1024; // 5 Mo
    private const MIME_OK = ['application/pdf', 'image/jpeg', 'image/png'];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ---------- Contrôle de saisie (côté serveur, PHP) ---------- */
    public function validate(array $d, ?array $file, ?int $ignoreId = null): array
    {
        $e = [];
        $nameRx = "/^[\p{L}][\p{L} '\-]{1,49}$/u";

        if (!preg_match($nameRx, trim($d['nom'] ?? '')))     $e['nom'] = "Nom invalide (lettres uniquement, 2 à 50 caractères).";
        if (!preg_match($nameRx, trim($d['prenom'] ?? '')))  $e['prenom'] = "Prénom invalide (lettres uniquement, 2 à 50 caractères).";

        $date = $d['date_naissance'] ?? '';
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt || $dt->format('Y-m-d') !== $date) {
            $e['date_naissance'] = "Date de naissance invalide.";
        } else {
            $age = $dt->diff(new DateTime())->y;
            if ($dt > new DateTime() || $age > 120) $e['date_naissance'] = "Date de naissance incohérente.";
        }

        if (!in_array($d['genre'] ?? '', ['homme', 'femme'], true)) $e['genre'] = "Choisissez un genre.";

        $email = trim($d['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $e['email'] = "Adresse e-mail invalide.";
        } elseif ($this->emailExists($email, $ignoreId)) {
            $e['email'] = "Cette adresse e-mail est déjà utilisée.";
        }

        if (!preg_match('/^(\+216)?[0-9]{8}$/', trim($d['telephone'] ?? ''))) {
            $e['telephone'] = "Numéro invalide (8 chiffres, +216 accepté).";
        }

        $heb = trim($d['hebergement'] ?? '');
        if ($heb !== '' && mb_strlen($heb) > 150) $e['hebergement'] = "Lieu d'hébergement trop long (150 max).";

        if (!in_array($d['role'] ?? '', ['client', 'medecin'], true)) $e['role'] = "Type de compte invalide.";

        if (($d['role'] ?? '') === 'medecin') {
            if (!preg_match($nameRx, trim($d['specialite'] ?? ''))) $e['specialite'] = "Spécialité invalide.";

            if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
                $e['preuve_identite'] = "Ajoutez votre justificatif de médecin certifié.";
            } elseif ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > self::MAX_SIZE) {
                $e['preuve_identite'] = "Fichier invalide ou supérieur à 5 Mo.";
            } else {
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
                if (!in_array($mime, self::MIME_OK, true)) $e['preuve_identite'] = "Formats acceptés : PDF, JPG, PNG.";
            }
        }
        return $e;
    }

    /* ---------- Inscription : valide, construit le modèle, enregistre ---------- */
    public function register(array $post, ?array $file): array
    {
        $errors = $this->validate($post, $file);
        if ($errors) return ['ok' => false, 'errors' => $errors];

        $nom        = trim($post['nom']);
        $prenom     = trim($post['prenom']);
        $date       = $post['date_naissance'];
        $genre      = $post['genre'];
        $email      = trim($post['email']);
        $telephone  = trim($post['telephone']);
        $hebergement = trim($post['hebergement'] ?? '');
        if ($hebergement === '') $hebergement = null;

        if ($post['role'] === 'medecin') {
            $path = $this->saveFile($file);
            $user = new Medecin(null, $nom, $prenom, $date, $genre, $email, $telephone,
                                $hebergement, trim($post['specialite']), $path);
        } else {
            $user = new User(null, $nom, $prenom, $date, $genre, $email, $telephone, $hebergement);
        }
        $this->add($user);
        return ['ok' => true, 'errors' => []];
    }

    /* ---------- CRUD ---------- */
    public function add(User $u): bool
    {
        $isDoc = $u instanceof Medecin;
        $st = $this->db->prepare(
            "INSERT INTO users (nom, prenom, date_naissance, genre, email, telephone, hebergement, role, specialite, preuve_identite)
             VALUES (?,?,?,?,?,?,?,?,?,?)"
        );
        return $st->execute([
            $u->getNom(), $u->getPrenom(), $u->getDateNaissance(), $u->getGenre(),
            $u->getEmail(), $u->getTelephone(), $u->getHebergement(), $u->getRole(),
            $isDoc ? $u->getSpecialite() : null,
            $isDoc ? $u->getPreuveIdentite() : null,
        ]);
    }

    public function update(User $u): bool
    {
        $isDoc = $u instanceof Medecin;
        $st = $this->db->prepare(
            "UPDATE users SET nom=?, prenom=?, date_naissance=?, genre=?, email=?, telephone=?,
             hebergement=?, specialite=?, preuve_identite=? WHERE id=?"
        );
        return $st->execute([
            $u->getNom(), $u->getPrenom(), $u->getDateNaissance(), $u->getGenre(),
            $u->getEmail(), $u->getTelephone(), $u->getHebergement(),
            $isDoc ? $u->getSpecialite() : null,
            $isDoc ? $u->getPreuveIdentite() : null,
            $u->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }

    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $st = $this->db->prepare("SELECT * FROM users WHERE id=?");
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /* ---------- Outils privés ---------- */
    private function emailExists(string $email, ?int $ignoreId): bool
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email=? AND id<>?");
        $st->execute([$email, $ignoreId ?? 0]);
        return (int) $st->fetchColumn() > 0;
    }

    private function saveFile(array $file): string
    {
        $ext = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png']
               [(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name'])];
        $name = bin2hex(random_bytes(12)) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR . $name);
        return $name;
    }
}
