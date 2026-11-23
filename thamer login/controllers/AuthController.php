<?php
/**
 * controllers/AuthController.php
 * ALL the business logic: SQL, hashing, validation, sessions, CSRF, roles, OAuth.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private PDO $db;
    private array $oauth;
    private array $mail;

    private const PROVIDERS = [
        'google'  => ['label' => 'Google',
                      'authorize' => 'https://accounts.google.com/o/oauth2/v2/auth',
                      'token'     => 'https://oauth2.googleapis.com/token',
                      'scope'     => 'openid email profile',
                      'prompt'    => 'select_account'],
        'github'  => ['label' => 'GitHub',
                      'authorize' => 'https://github.com/login/oauth/authorize',
                      'token'     => 'https://github.com/login/oauth/access_token',
                      'scope'     => 'read:user user:email',
                      'prompt'    => 'select_account'],
        'discord' => ['label' => 'Discord',
                      'authorize' => 'https://discord.com/oauth2/authorize',
                      'token'     => 'https://discord.com/api/oauth2/token',
                      'scope'     => 'identify email',
                      'prompt'    => 'consent'],
    ];

    public function __construct()
    {
        $this->db = Database::getConnexion();
        $this->oauth = require __DIR__ . '/../config/oauth.php';
        $this->mail = require __DIR__ . '/../config/mail.php';
    }

    // ==============================================================
    // SESSION STATE
    // ==============================================================
    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /** Where a logged-in user lands: admin -> backoffice, user -> frontoffice */
    public static function homeUrl(): string
    {
        return 'index.php?action=' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard');
    }

    /** Opens the session for a user (session ID regenerated: anti session-fixation) */
    private function startSession(UserModel $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['last_activity'] = time();
    }

    // ==============================================================
    // DATA ACCESS (prepared statements - SQL injection protection)
    // ==============================================================
    private function existsByEmailOrUsername(string $email, string $username): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
        $stmt->execute(['email' => $email, 'username' => $username]);
        return (bool) $stmt->fetch();
    }

    private function insertUser(UserModel $user): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password, role, provider, provider_id)
             VALUES (:username, :email, :password, :role, :provider, :provider_id)'
        );
        return $stmt->execute([
            'username'    => $user->getUsername(),
            'email'       => $user->getEmail(),
            'password'    => $user->getPassword(),
            'role'        => $user->getRole(),
            'provider'    => $user->getProvider() ?: null,
            'provider_id' => $user->getProviderId() ?: null,
        ]);
    }

    private function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    private function findByProvider(string $provider, string $providerId): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE provider = :p AND provider_id = :pid LIMIT 1');
        $stmt->execute(['p' => $provider, 'pid' => $providerId]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    // ==============================================================
    // REGISTER
    // ==============================================================
    public function register(array $post): array
    {
        $errors = [];
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors[] = "Jeton de sécurité invalide. Veuillez réessayer.";
            return $errors;
        }

        $username = trim($post['username'] ?? '');
        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';
        $passwordConfirm = $post['password_confirm'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $errors[] = "Tous les champs sont obligatoires.";
        }
        if (!preg_match('/^[a-zA-Z0-9_\-]{3,50}$/', $username)) {
            $errors[] = "Nom d'utilisateur : 3 à 50 caractères (lettres, chiffres, _ ou -).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->existsByEmailOrUsername($email, $username)) {
            $errors[] = "Cet email ou nom d'utilisateur est déjà utilisé.";
            return $errors;
        }

        $user = new UserModel();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT)); // bcrypt
        $user->setRole('user');                                          // never self-promote to admin

        if (!$this->insertUser($user)) {
            $errors[] = "Une erreur est survenue lors de la création du compte.";
        }
        return $errors; // empty = success
    }

    // ==============================================================
    // LOGIN / LOGOUT
    // ==============================================================
    public function login(array $post): array
    {
        $errors = [];

        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors[] = "Jeton de sécurité invalide. Veuillez réessayer.";
            return $errors;
        }

        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';

        if ($email === '' || $password === '') {
            $errors[] = "Veuillez saisir votre email et votre mot de passe.";
            return $errors;
        }

        $user = $this->findByEmail($email);

        // Deliberately generic message (never reveal whether the email exists)
        if (!$user || !password_verify($password, $user->getPassword())) {
            $errors[] = "Email ou mot de passe incorrect.";
            return $errors;
        }

        $this->startSession($user);
        if (!empty($post['remember'])) {
            $this->issueRememberToken($user->getId());
        }
        return []; // success
    }

    public function logout(): void
    {
        // forget the "remember me" cookie + its database token
        $cookie = $_COOKIE['remember'] ?? '';
        if (is_string($cookie) && strpos($cookie, '.') !== false) {
            $this->deleteRememberToken(explode('.', $cookie)[0]);
        }
        $this->setRememberCookie('', time() - 42000);

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    // ==============================================================
    // OAUTH (Google / GitHub / Discord)
    // ==============================================================
    /** Labels of all providers (used by the view to draw the buttons) */
    public function getProviders(): array
    {
        return array_map(fn(array $p) => $p['label'], self::PROVIDERS);
    }

    private function isConfigured(string $provider): bool
    {
        return isset(self::PROVIDERS[$provider])
            && !empty($this->oauth[$provider]['client_id'])
            && !empty($this->oauth[$provider]['client_secret']);
    }

    private function redirectUri(): string
    {
        return rtrim($this->oauth['base_url'], '/') . '/oauth_callback.php';
    }

    /** Step 1: send the visitor to the provider. Redirects (and exits) on success. */
    public function oauthStart(string $provider): array
    {
        if (!$this->isConfigured($provider)) {
            $label = self::PROVIDERS[$provider]['label'] ?? 'ce service';
            return ["La connexion avec $label n'est pas configurée (voir config/oauth.php)."];
        }

        $state = bin2hex(random_bytes(16)); // anti-CSRF for the OAuth round-trip
        $_SESSION['oauth'] = ['provider' => $provider, 'state' => $state];

        $query = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->oauth[$provider]['client_id'],
            'redirect_uri'  => $this->redirectUri(),
            'scope'         => self::PROVIDERS[$provider]['scope'],
            'state'         => $state,
            'prompt'        => self::PROVIDERS[$provider]['prompt'], // always let the visitor pick the account
        ], '', '&', PHP_QUERY_RFC3986);

        header('Location: ' . self::PROVIDERS[$provider]['authorize'] . '?' . $query);
        exit;
    }

    /** Step 2: the provider sends the visitor back with ?code=...&state=... */
    public function oauthCallback(array $get): array
    {
        $saved = $_SESSION['oauth'] ?? null;
        unset($_SESSION['oauth']);

        if (!is_array($saved)) {
            return ["Session expirée, veuillez réessayer."];
        }
        $provider = $saved['provider'];
        $label = self::PROVIDERS[$provider]['label'];

        if (isset($get['error'])) {
            return ["Connexion avec $label annulée."];
        }
        $state = $get['state'] ?? '';
        $code = $get['code'] ?? '';
        if (!is_string($state) || !is_string($code) || $code === '' || !hash_equals($saved['state'], $state)) {
            return ["Tentative de connexion invalide. Veuillez réessayer."];
        }

        try {
            $profile = $this->fetchProfile($provider, $this->exchangeCode($provider, $code));
        } catch (Throwable $e) {
            error_log('OAuth error (' . $provider . '): ' . $e->getMessage());
            return ["Impossible de terminer la connexion avec $label."];
        }

        // Known social account -> log in. Otherwise -> create it.
        $user = $profile['id'] !== '' ? $this->findByProvider($provider, $profile['id']) : null;

        if (!$user) {
            $email = strtolower(trim((string) $profile['email']));
            if ($profile['id'] === '' || $email === '' || !$profile['verified'] || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ["$label n'a pas fourni d'adresse email vérifiée."];
            }
            $existing = $this->findByEmail($email);
            if ($existing && $existing->getProvider() === '') {
                // password account: its email was never verified, so no automatic link
                return ["Un compte existe déjà avec cet email : connectez-vous avec votre mot de passe."];
            }
            if ($existing) {
                // same person, account created through another verified provider: log in
                $this->startSession($existing);
                return [];
            }
            $user = new UserModel();
            $user->setUsername($this->makeUsername($profile['name'] ?: explode('@', $email)[0]));
            $user->setEmail($email);
            $user->setPassword(password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT)); // unusable password
            $user->setRole('user');
            $user->setProvider($provider);
            $user->setProviderId($profile['id']);

            if (!$this->insertUser($user)) {
                return ["Une erreur est survenue lors de la création du compte."];
            }
            $user = $this->findByProvider($provider, $profile['id']);
        }

        $this->startSession($user);
        return [];
    }

    /** Builds a valid, unique username from a display name */
    private function makeUsername(string $name): string
    {
        $base = substr(preg_replace('/[^a-zA-Z0-9_\-]/', '', $name), 0, 40);
        if (strlen($base) < 3) {
            $base = 'user';
        }
        $username = $base;
        while ($this->existsByEmailOrUsername('', $username)) {
            $username = $base . random_int(100, 99999);
        }
        return $username;
    }

    private function exchangeCode(string $provider, string $code): string
    {
        [$status, $data] = $this->http('POST', self::PROVIDERS[$provider]['token'], ['Accept: application/json'], http_build_query([
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->redirectUri(),
            'client_id'     => $this->oauth[$provider]['client_id'],
            'client_secret' => $this->oauth[$provider]['client_secret'],
        ]));
        if ($status !== 200 || empty($data['access_token']) || !is_string($data['access_token'])) {
            throw new RuntimeException("token exchange failed (HTTP $status)");
        }
        return $data['access_token'];
    }

    /** @return array{id:string,email:?string,verified:bool,name:string} */
    private function fetchProfile(string $provider, string $token): array
    {
        $auth = 'Authorization: Bearer ' . $token;

        if ($provider === 'google') {
            $d = $this->getJson('https://openidconnect.googleapis.com/v1/userinfo', [$auth]);
            return ['id' => (string) ($d['sub'] ?? ''), 'email' => $d['email'] ?? null,
                    'verified' => filter_var($d['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'name' => (string) ($d['name'] ?? '')];
        }

        if ($provider === 'github') {
            $h = [$auth, 'Accept: application/vnd.github+json'];
            $u = $this->getJson('https://api.github.com/user', $h);
            $email = null;
            [$status, $emails] = $this->http('GET', 'https://api.github.com/user/emails', $h);
            if ($status === 200 && is_array($emails)) {
                foreach ($emails as $row) {            // first verified address (primary one comes first)
                    if (!empty($row['verified']) && !empty($row['primary'])) { $email = $row['email']; break; }
                    if ($email === null && !empty($row['verified'])) { $email = $row['email']; }
                }
            }
            return ['id' => (string) ($u['id'] ?? ''), 'email' => $email, 'verified' => $email !== null,
                    'name' => (string) (($u['name'] ?? '') ?: ($u['login'] ?? ''))];
        }

        // discord
        $d = $this->getJson('https://discord.com/api/users/@me', [$auth]);
        return ['id' => (string) ($d['id'] ?? ''), 'email' => $d['email'] ?? null,
                'verified' => ($d['verified'] ?? false) === true,
                'name' => (string) (($d['global_name'] ?? '') ?: ($d['username'] ?? ''))];
    }

    private function getJson(string $url, array $headers): array
    {
        [$status, $data] = $this->http('GET', $url, $headers);
        if ($status !== 200 || $data === null) {
            throw new RuntimeException("profile request failed (HTTP $status)");
        }
        return $data;
    }

    /** Minimal cURL wrapper. @return array{0:int,1:?array} HTTP status + decoded JSON */
    private function http(string $method, string $url, array $headers = [], ?string $body = null): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_USERAGENT      => 'MyApp-OAuth',   // required by GitHub
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException('HTTP request failed: ' . $error);
        }
        $json = json_decode((string) $raw, true);
        return [$status, is_array($json) ? $json : null];
    }

    // ==============================================================
    // REMEMBER ME (cookie "selector.validator", only a hash is stored in the DB)
    // ==============================================================
    private function setRememberCookie(string $value, int $expires): void
    {
        setcookie('remember', $value, [
            'expires'  => $expires,
            'path'     => rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !empty($_SERVER['HTTPS']),
        ]);
    }

    private function issueRememberToken(int $userId): void
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(32));
        $expires = time() + 30 * 86400; // 30 days

        $stmt = $this->db->prepare(
            'INSERT INTO remember_tokens (user_id, selector, validator_hash, expires_at)
             VALUES (:u, :s, :h, :e)'
        );
        $stmt->execute(['u' => $userId, 's' => $selector, 'h' => hash('sha256', $validator), 'e' => $expires]);
        $this->setRememberCookie($selector . '.' . $validator, $expires);
    }

    private function deleteRememberToken(string $selector): void
    {
        $this->db->prepare('DELETE FROM remember_tokens WHERE selector = :s')->execute(['s' => $selector]);
    }

    /** Called on every request by index.php: silently logs the visitor back in */
    public function loginFromCookie(): void
    {
        $cookie = $_COOKIE['remember'] ?? '';
        if (self::isAuthenticated() || !is_string($cookie) || substr_count($cookie, '.') !== 1) {
            return;
        }
        [$selector, $validator] = explode('.', $cookie);

        $stmt = $this->db->prepare('SELECT * FROM remember_tokens WHERE selector = :s AND expires_at > :now LIMIT 1');
        $stmt->execute(['s' => $selector, 'now' => time()]);
        $row = $stmt->fetch();

        if ($row && hash_equals($row['validator_hash'], hash('sha256', $validator))) {
            $user = $this->findById((int) $row['user_id']);
            $this->deleteRememberToken($selector);      // one-time token: rotated at each use
            if ($user) {
                $this->startSession($user);
                $this->issueRememberToken($user->getId());
                return;
            }
        }
        // unknown, expired or tampered cookie -> clean everything
        $this->deleteRememberToken($selector);
        $this->setRememberCookie('', time() - 42000);
    }

    // ==============================================================
    // FORGOT / RESET PASSWORD (email sent through Brevo)
    // ==============================================================
    /** Step 1: the visitor gives an email. Same answer whether or not the account exists. */
    public function requestReset(array $post): array
    {
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ["Jeton de sécurité invalide. Veuillez réessayer."];
        }
        $email = trim($post['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["Adresse email invalide."];
        }

        $user = $this->findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->db->prepare('DELETE FROM password_resets WHERE user_id = :u')->execute(['u' => $user->getId()]);
            $this->db->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:u, :h, :e)')
                ->execute(['u' => $user->getId(), 'h' => hash('sha256', $token), 'e' => time() + 3600]); // 60 min
            $this->sendResetEmail($user, rtrim($this->oauth['base_url'], '/') . '/index.php?action=reset&token=' . $token);
        }
        return []; // success (never reveals if the email exists)
    }

    private function sendResetEmail(UserModel $user, string $link): void
    {
        $key = $this->mail['brevo_api_key'] ?? '';
        $from = $this->mail['from_email'] ?? '';

        if ($key === '' || $from === '') { // dev mode: no Brevo configured -> link goes to the Apache error log
            error_log('Password reset link for ' . $user->getEmail() . ': ' . $link);
            return;
        }

        $name = htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8');
        $html = "<p>Bonjour $name,</p>"
              . "<p>Pour choisir un nouveau mot de passe, cliquez sur le lien ci-dessous (valable 60 minutes) :</p>"
              . "<p><a href=\"$link\">Réinitialiser mon mot de passe</a></p>"
              . "<p>Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>";
        try {
            [$status, $data] = $this->http('POST', 'https://api.brevo.com/v3/smtp/email',
                ['api-key: ' . $key, 'Content-Type: application/json', 'Accept: application/json'],
                json_encode([
                    'sender'      => ['name' => $this->mail['from_name'] ?? 'MyApp', 'email' => $from],
                    'to'          => [['email' => $user->getEmail(), 'name' => $user->getUsername()]],
                    'subject'     => 'Réinitialisation de votre mot de passe',
                    'htmlContent' => $html,
                ]));
            if ($status !== 201) {
                error_log('Brevo error HTTP ' . $status . ': ' . json_encode($data));
            }
        } catch (Throwable $e) {
            error_log('Brevo error: ' . $e->getMessage());
        }
    }

    private function findResetUserId(string $token): ?int
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        $stmt = $this->db->prepare('SELECT user_id FROM password_resets WHERE token_hash = :h AND expires_at > :now LIMIT 1');
        $stmt->execute(['h' => hash('sha256', $token), 'now' => time()]);
        $id = $stmt->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    public function isResetTokenValid(string $token): bool
    {
        return $this->findResetUserId($token) !== null;
    }

    /** Step 2: the visitor chooses a new password from the emailed link */
    public function resetPassword(array $post): array
    {
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ["Jeton de sécurité invalide. Veuillez réessayer."];
        }
        $token = is_string($post['token'] ?? null) ? $post['token'] : '';
        $password = $post['password'] ?? '';
        $confirm = $post['password_confirm'] ?? '';

        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!empty($errors)) {
            return $errors;
        }

        $userId = $this->findResetUserId($token);
        if ($userId === null) {
            return ["Lien invalide ou expiré. Refaites une demande."];
        }

        $this->db->prepare('UPDATE users SET password = :p WHERE id = :id')
            ->execute(['p' => password_hash($password, PASSWORD_DEFAULT), 'id' => $userId]);
        $this->db->prepare('DELETE FROM password_resets WHERE user_id = :u')->execute(['u' => $userId]);
        $this->db->prepare('DELETE FROM remember_tokens WHERE user_id = :u')->execute(['u' => $userId]); // sign out other devices
        return [];
    }

    // ==============================================================
    // USER SPACE (frontoffice)
    // ==============================================================
    /** Redirects and stops execution if the user is not authenticated */
    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    // ==============================================================
    // ROLE CONTROL
    // ==============================================================
    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role;
    }

    /** Redirects / blocks (403) if the user doesn't have the required role */
    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (($_SESSION['role'] ?? null) !== $role) {
            http_response_code(403);
            die('Accès refusé : vous n\'avez pas les droits nécessaires.');
        }
    }

    // ==============================================================
    // ADMIN BACKOFFICE DATA
    // ==============================================================
    /** @return UserModel[] */
    public function getAllUsers(): array
    {
        $rows = $this->db->query('SELECT * FROM users ORDER BY id ASC')->fetchAll();
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

    // ==============================================================
    // CSRF
    // ==============================================================
    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken(?string $token): bool
    {
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
