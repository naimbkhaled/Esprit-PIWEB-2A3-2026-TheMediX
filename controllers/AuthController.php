<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private PDO $db;
    private MyApp1122AuthController $extendedAuth;

    public function __construct()
    {
        $this->db = Database::getConnexion();
        require_once __DIR__ . '/../myapp1122/controllers/AuthController.php';
        $this->extendedAuth = new MyApp1122AuthController();
    }

    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function homeUrl(): string
    {
        return 'index.php?action=' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard');
    }

    public function getProviders(): array
    {
        return $this->extendedAuth->getProviders();
    }

    public function oauthStart(string $provider): array
    {
        return $this->extendedAuth->oauthStart($provider);
    }

    public function oauthCallback(array $query): array
    {
        return $this->extendedAuth->oauthCallback($query);
    }

    public function requestReset(array $post): array
    {
        return $this->extendedAuth->requestReset($post);
    }

    public function resetPassword(array $post): array
    {
        return $this->extendedAuth->resetPassword($post);
    }

    public function isResetTokenValid(string $token): bool
    {
        return $this->extendedAuth->isResetTokenValid($token);
    }

    public function loginFromCookie(): void
    {
        $this->extendedAuth->loginFromCookie();
    }

    private function rememberFor(int $userId): void
    {
        $this->extendedAuth->rememberFor($userId);
    }

    private function existsByEmailOrUsername(string $email, string $username): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
        $stmt->execute(['email' => $email, 'username' => $username]);
        return (bool) $stmt->fetch();
    }

    private function insertUser(UserModel $user): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)'
        );
        return $stmt->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
        ]);
    }

    public function register(array $post, ?array $proof = null): array
    {
        $errors = [];

        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors['general'] = "Invalid security token. Please try again.";
            return $errors;
        }

        $value = static fn(string $key): string => is_string($post[$key] ?? null) ? trim($post[$key]) : '';
        $nom = $value('nom');
        $prenom = $value('prenom');
        $dateNaissance = $value('date_naissance');
        $genre = $value('genre');
        $email = $value('email');
        $telephone = $value('telephone');
        $hebergement = $value('hebergement');
        $accountType = $value('role');
        $specialite = $value('specialite');
        $password = is_string($post['password'] ?? null) ? $post['password'] : '';
        $passwordConfirm = is_string($post['password_confirm'] ?? null) ? $post['password_confirm'] : '';
        $namePattern = "/^[\\p{L}][\\p{L} '\\-]{1,49}$/u";

        if (!preg_match($namePattern, $nom)) {
            $errors['nom'] = 'Nom invalide (2 à 50 caractères).';
        }
        if (!preg_match($namePattern, $prenom)) {
            $errors['prenom'] = 'Prénom invalide (2 à 50 caractères).';
        }
        $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dateNaissance);
        $today = new DateTimeImmutable('today');
        if (!$birthDate || $birthDate->format('Y-m-d') !== $dateNaissance
            || $birthDate > $today || $birthDate->diff($today)->y > 120) {
            $errors['date_naissance'] = 'Date de naissance invalide.';
        }
        if (!in_array($genre, ['homme', 'femme'], true)) {
            $errors['genre'] = 'Choisissez un genre.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
            $errors['email'] = 'Adresse e-mail invalide.';
        }
        if (!preg_match('/^(\\+216)?[0-9]{8}$/', $telephone)) {
            $errors['telephone'] = 'Numéro invalide (8 chiffres, +216 accepté).';
        }
        if (strlen($hebergement) > 150) {
            $errors['hebergement'] = 'Lieu d’hébergement trop long (150 caractères maximum).';
        }
        if (!in_array($accountType, ['client', 'medecin'], true)) {
            $errors['role'] = 'Type de compte invalide.';
        }
        if ($accountType === 'medecin' && !preg_match($namePattern, $specialite)) {
            $errors['specialite'] = 'Spécialité invalide.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
        }
        $proofMime = null;
        if ($accountType === 'medecin') {
            $uploadError = $proof['error'] ?? UPLOAD_ERR_NO_FILE;
            $temporaryUpload = is_array($proof) && is_string($proof['tmp_name'] ?? null)
                ? $proof['tmp_name']
                : '';
            if ($uploadError === UPLOAD_ERR_NO_FILE) {
                $errors['preuve_identite'] = 'Ajoutez votre justificatif de médecin certifié.';
            } elseif ($uploadError !== UPLOAD_ERR_OK || !is_uploaded_file($temporaryUpload)
                || ($proof['size'] ?? 0) > 5 * 1024 * 1024) {
                $errors['preuve_identite'] = 'Fichier invalide ou supérieur à 5 Mo.';
            } else {
                $proofMime = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryUpload);
                if (!in_array($proofMime, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
                    $errors['preuve_identite'] = 'Formats acceptés : PDF, JPG, PNG.';
                }
            }
        }
        if ($errors) {
            return $errors;
        }

        $duplicate = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $duplicate->execute([$email]);
        if ($duplicate->fetch()) {
            $errors['email'] = 'Cette adresse e-mail est déjà utilisée.';
            return $errors;
        }

        $baseUsername = strtolower($prenom . '_' . $nom);
        $baseUsername = preg_replace('/[^a-z0-9_-]+/', '_', $baseUsername) ?? '';
        $baseUsername = trim(substr($baseUsername, 0, 40), '_-');
        if ($baseUsername === '') {
            $baseUsername = 'user';
        }
        $username = $baseUsername;
        for ($attempt = 0; $this->existsByEmailOrUsername('', $username); $attempt++) {
            if ($attempt >= 10) {
                $errors['general'] = 'Impossible de créer un identifiant unique. Veuillez réessayer.';
                return $errors;
            }
            $username = substr($baseUsername, 0, 40) . random_int(100, 99999);
        }

        $proofName = null;
        $uploadPath = null;
        if ($accountType === 'medecin') {
            $extension = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'][$proofMime];
            $proofName = bin2hex(random_bytes(12)) . '.' . $extension;
            $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
            if ($documentRoot === '') {
                return ['preuve_identite' => 'Le justificatif n’a pas pu être enregistré.'];
            }
            $uploadDirectory = dirname($documentRoot) . DIRECTORY_SEPARATOR . 'medix-private-uploads';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0700, true) && !is_dir($uploadDirectory)) {
                return ['preuve_identite' => 'Le justificatif n’a pas pu être enregistré.'];
            }
            $uploadPath = $uploadDirectory . DIRECTORY_SEPARATOR . $proofName;
            if (!move_uploaded_file($proof['tmp_name'], $uploadPath)) {
                $errors['preuve_identite'] = 'Le justificatif n’a pas pu être enregistré.';
                return $errors;
            }
        }

        try {
            $this->db->beginTransaction();
            $insertUser = $this->db->prepare(
                'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)'
            );
            $insertUser->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), 'user']);
            $userId = (int) $this->db->lastInsertId();

            $insertProfile = $this->db->prepare(
                'INSERT INTO user_profiles
                    (user_id, nom, prenom, date_naissance, genre, telephone, hebergement, account_type, specialite, preuve_identite)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insertProfile->execute([
                $userId, $nom, $prenom, $dateNaissance, $genre, $telephone,
                $hebergement !== '' ? $hebergement : null, $accountType,
                $accountType === 'medecin' ? $specialite : null, $proofName,
            ]);
            $this->db->commit();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if ($proofName !== null && $uploadPath !== null && is_file($uploadPath)) {
                unlink($uploadPath);
            }
            error_log('Account registration failed: ' . $e->getMessage());
            $errors['general'] = 'Une erreur est survenue lors de la création du compte.';
            return $errors;
        }
        return $errors;
    }

    private function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    public function login(array $post): array
    {
        $errors = [];

        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors[] = "Invalid security token. Please try again.";
            return $errors;
        }

        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';

        if ($email === '' || $password === '') {
            $errors[] = "Please enter your email and password.";
            return $errors;
        }

        $user = $this->findByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            $errors[] = "Incorrect email or password.";
            return $errors;
        }

        if (!$user->isActive()) {
            $errors[] = 'Ce compte a été désactivé.';
            return $errors;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['last_activity'] = time();
        if (!empty($post['remember'])) {
            $this->rememberFor($user->getId());
        }

        return [];
    }

    public function deactivateAccount(int $id, array $post): array
    {
        $errors = [];
        $token = is_string($post['csrf_token'] ?? null) ? $post['csrf_token'] : null;
        if (!self::verifyCsrfToken($token)) {
            $errors[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            return $errors;
        }

        $user = $this->findById($id);
        if (!$user) {
            $errors[] = 'Compte introuvable.';
            return $errors;
        }
        if ($user->getRole() === 'admin') {
            $errors[] = 'Un compte administrateur ne peut pas être désactivé depuis cette page.';
            return $errors;
        }
        if (!$user->isActive()) {
            $errors[] = 'Ce compte est déjà désactivé.';
            return $errors;
        }

        $password = is_string($post['password'] ?? null) ? $post['password'] : '';
        if (!password_verify($password, $user->getPassword())) {
            $errors[] = 'Mot de passe incorrect.';
            return $errors;
        }

        $stmt = $this->db->prepare('UPDATE users SET is_active = 0 WHERE id = :id AND is_active = 1');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() !== 1) {
            $errors[] = 'Le compte n’a pas pu être désactivé. Veuillez réessayer.';
        } else {
            $this->db->prepare('DELETE FROM remember_tokens WHERE user_id = :id')->execute(['id' => $id]);
        }

        return $errors;
    }

    public function logout(): void
    {
        $this->extendedAuth->logout();
    }

    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role;
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (($_SESSION['role'] ?? null) !== $role) {
            http_response_code(403);
            die('Access denied: you do not have the required permissions.');
        }
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY id ASC');
        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => UserModel::fromArray($row), $rows);
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return (int) $stmt->fetchColumn();
    }

    public function countAllUsers(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public static function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
