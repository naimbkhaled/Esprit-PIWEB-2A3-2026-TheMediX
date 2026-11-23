<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
class AuthController
{
private PDO $db;
public function __construct()
{
$this->db = Database::getConnexion();
}
// ==============================================================
// SESSION STATE
// ==============================================================
public static function isAuthenticated(): bool
{
return !empty($_SESSION['user_id']);
}
// ==============================================================
// DATA ACCESS (prepared statements - SQL injection protection)
// ==============================================================
private function existsByEmailOrUsername(string $email, string $username): bool
 {
$stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email OR username =
:username LIMIT 1');
$stmt->execute(['email' => $email, 'username' => $username]);
return (bool) $stmt->fetch();
}
 private function insertUser(UserModel $user): bool
{
$stmt = $this->db->prepare(
'INSERT INTO users (username, email, password, role) VALUES (:username, :email,
:password, :role)'
);
return $stmt->execute([
'username' => $user->getUsername(),
'email' => $user->getEmail(),
'password' => $user->getPassword(),
'role' => $user->getRole(),
]);
}
// ==============================================================
// REGISTER
// ==============================================================
public function register(array $post): array
{
$errors = [];
if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
$errors[] = "Invalid security token. Please try again.";
return $errors;
} 
$username = trim($post['username'] ?? '');
$email = trim($post['email'] ?? '');
$password = $post['password'] ?? '';
$passwordConfirm = $post['password_confirm'] ?? '';
if ($username === '' || $email === '' || $password === '') {
$errors[] = "All fields are required.";
}
if (!preg_match('/^[a-zA-Z0-9_\-]{3,50}$/', $username)) {
$errors[] = "Username must be 3 to 50 characters (letters, digits, _ or -).";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$errors[] = "Invalid email address.";
}
if (strlen($password) < 8) {
$errors[] = "Password must be at least 8 characters long.";
}
if ($password !== $passwordConfirm) {
$errors[] = "Passwords do not match.";
}
if (!empty($errors)) {
return $errors;
}
if ($this->existsByEmailOrUsername($email, $username)) {
$errors[] = "This email or username is already taken.";
return $errors;
 }
 // Secure password hashing (bcrypt via password_hash)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// Build the entity (role forced to 'user' - no self-promotion to admin)
$user = new UserModel();
$user->setUsername($username);
$user->setEmail($email);
$user->setPassword($hashedPassword);
$user->setRole('user');
if (!$this->insertUser($user)) {
$errors[] = "An error occurred while creating your account.";
}
return $errors; // empty array = success
}
// ==============================================================
// LOGIN
// ==============================================================
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
if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
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
// Deliberately generic message (never reveal whether the email exists)
if (!$user || !password_verify($password, $user->getPassword())) {
$errors[] = "Incorrect email or password.";
return $errors;
}
if (!$user->isActive()) {
    $errors[] = "This account has been deactivated.";
    return $errors;
}
if ($user->isTwoFactorEnabled()) {
    session_regenerate_id(true);
    $_SESSION['pending_2fa_user_id'] = $user->getId();
    $_SESSION['pending_2fa_time'] = time();
    $_SESSION['pending_2fa_attempts'] = 0;
    return []; // password OK, but code still required
}

$this->startSession($user);
return []; // success
}
// ==============================================================
// LOGOUT
// ==============================================================
public function logout(): void
{
$_SESSION = [];
if (ini_get('session.use_cookies')) {
$params = session_get_cookie_params();
setcookie(
session_name(),
'',
time() - 42000,
$params['path'],
$params['domain'],
$params['secure'],
$params['httponly']
);
}
session_destroy();
}
// ==============================================================
// USER SPACE (frontoffice)
// ==============================================================
public function findById(int $id): ?UserModel
{
$stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();
return $row ? UserModel::fromArray($row) : null;
}
/**
* Redirects and stops execution if the user is not authenticated
*/
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
/**
* Redirects and stops execution if the user doesn't have the required role
*/
public static function requireRole(string $role): void
{
self::requireAuth();
if (($_SESSION['role'] ?? null) !== $role) {
http_response_code(403);
die('Access denied: you do not have the required permissions.');
}
}
// ==============================================================
// ADMIN BACKOFFICE DATA
// ==============================================================
/**
* Retrieves all users (used by the admin backoffice)
* @return UserModel[]
*/
public function getAllUsers(): array
{
$stmt = $this->db->query('SELECT * FROM users ORDER BY id ASC');
$rows = $stmt->fetchAll();
return array_map(fn(array $row) => UserModel::fromArray($row), $rows);
}
/**
* Counts users by role (used by the admin dashboard stats)
*/
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

// ==============================================================
// DEACTIVATE ACCOUNT
// ==============================================================
public function deactivateAccount(int $id, array $post): array
{
    $errors = [];

    if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
        $errors[] = "Invalid security token. Please try again.";
        return $errors;
    }

    $user = $this->findById($id);
    if (!$user) {
        $errors[] = "Account not found.";
        return $errors;
    }

    // Prevent the admin from locking themselves out
    if ($user->getRole() === 'admin') {
        $errors[] = "An administrator account cannot be deactivated here.";
        return $errors;
    }

    // Ask for the password again before a sensitive action
    if (!password_verify($post['password'] ?? '', $user->getPassword())) {
        $errors[] = "Incorrect password.";
        return $errors;
    }

    $stmt = $this->db->prepare('UPDATE users SET is_active = 0 WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return []; // success
}

// ==============================================================
// TWO-FACTOR AUTHENTICATION (TOTP)
// ==============================================================
private const BASE32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

private function startSession(UserModel $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user->getId();
    $_SESSION['username'] = $user->getUsername();
    $_SESSION['role'] = $user->getRole();
    $_SESSION['last_activity'] = time();
}

public static function hasPendingTwoFactor(): bool
{
    return !empty($_SESSION['pending_2fa_user_id']);
}

private function clearPendingTwoFactor(): void
{
    unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_time'], $_SESSION['pending_2fa_attempts']);
}

private function base32Encode(string $bin): string
{
    $bits = '';
    foreach (str_split($bin) as $c) {
        $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
    }
    $out = '';
    foreach (str_split($bits, 5) as $chunk) {
        $out .= self::BASE32[bindec(str_pad($chunk, 5, '0'))];
    }
    return $out;
}

private function base32Decode(string $b32): string
{
    $bits = '';
    foreach (str_split(strtoupper($b32)) as $c) {
        $pos = strpos(self::BASE32, $c);
        if ($pos === false) continue;
        $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
    }
    $out = '';
    foreach (str_split($bits, 8) as $byte) {
        if (strlen($byte) === 8) $out .= chr(bindec($byte));
    }
    return $out;
}

private function totpAt(string $secret, int $slice): string
{
    $key  = $this->base32Decode($secret);
    $msg  = pack('N*', 0) . pack('N*', $slice);
    $hash = hash_hmac('sha1', $msg, $key, true);
    $off  = ord($hash[19]) & 0x0F;
    $val  = ((ord($hash[$off]) & 0x7F) << 24)
          | ((ord($hash[$off + 1]) & 0xFF) << 16)
          | ((ord($hash[$off + 2]) & 0xFF) << 8)
          |  (ord($hash[$off + 3]) & 0xFF);
    return str_pad((string) ($val % 1000000), 6, '0', STR_PAD_LEFT);
}

private function verifyTotp(string $secret, string $code): bool
{
    $code = preg_replace('/\D/', '', $code);
    if ($secret === '' || strlen($code) !== 6) return false;
    $slice = intdiv(time(), 30);
    for ($i = -1; $i <= 1; $i++) { // tolerate 30s of clock drift
        if (hash_equals($this->totpAt($secret, $slice + $i), $code)) return true;
    }
    return false;
}

/** Secret shown during setup, kept in session until confirmed */
public function getSetupSecret(): string
{
    if (empty($_SESSION['2fa_setup_secret'])) {
        $_SESSION['2fa_setup_secret'] = $this->base32Encode(random_bytes(20));
    }
    return $_SESSION['2fa_setup_secret'];
}

public function getOtpauthUri(UserModel $user, string $secret): string
{
    return 'otpauth://totp/MyApp:' . rawurlencode($user->getEmail())
         . '?secret=' . $secret . '&issuer=MyApp';
}

public function enableTwoFactor(int $id, array $post): array
{
    if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
        return ["Invalid security token. Please try again."];
    }
    $secret = $_SESSION['2fa_setup_secret'] ?? '';
    if (!$this->verifyTotp($secret, (string) ($post['code'] ?? ''))) {
        return ["Invalid code. Check your app and try again."];
    }
    $stmt = $this->db->prepare(
        'UPDATE users SET two_factor_secret = :s, two_factor_enabled = 1 WHERE id = :id'
    );
    $stmt->execute(['s' => $secret, 'id' => $id]);
    unset($_SESSION['2fa_setup_secret']);
    return [];
}

public function disableTwoFactor(int $id, array $post): array
{
    if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
        return ["Invalid security token. Please try again."];
    }
    $user = $this->findById($id);
    if (!$user || !password_verify($post['password'] ?? '', $user->getPassword())) {
        return ["Incorrect password."];
    }
    if (!$this->verifyTotp($user->getTwoFactorSecret(), (string) ($post['code'] ?? ''))) {
        return ["Invalid code."];
    }
    $stmt = $this->db->prepare(
        'UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);
    return [];
}

/** Second step of login */
public function verifyTwoFactorLogin(array $post): array
{
    if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
        return ["Invalid security token. Please try again."];
    }
    if (!self::hasPendingTwoFactor() || time() - ($_SESSION['pending_2fa_time'] ?? 0) > 300) {
        $this->clearPendingTwoFactor();
        return ["Session expired. Please log in again."];
    }
    $_SESSION['pending_2fa_attempts'] = ($_SESSION['pending_2fa_attempts'] ?? 0) + 1;
    if ($_SESSION['pending_2fa_attempts'] > 5) {
        $this->clearPendingTwoFactor();
        return ["Too many attempts. Please log in again."];
    }

    $user = $this->findById((int) $_SESSION['pending_2fa_user_id']);
    if (!$user || !$user->isActive() || !$user->isTwoFactorEnabled()
        || !$this->verifyTotp($user->getTwoFactorSecret(), (string) ($post['code'] ?? ''))) {
        return ["Invalid code."];
    }

    $this->clearPendingTwoFactor();
    $this->startSession($user);
    return [];
}

// ==============================================================
// FORGOT / RESET PASSWORD
// ==============================================================
private function sendMail(string $to, string $subject, string $body): bool
{
    $cfg = require __DIR__ . '/../config/mail.php';
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $cfg['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $cfg['username'];
        $mail->Password   = $cfg['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $cfg['port'];
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom($cfg['from'], $cfg['from_name']);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        return $mail->send();
} catch (\Throwable $e) {
    error_log('Mail error: ' . $e->getMessage());
    die('Mail error: ' . $e->getMessage()); // TEMPORARY, remove after debugging
}
}

public function requestPasswordReset(array $post): array
{
    if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
        return ["Invalid security token. Please try again."];
    }

    $email = trim($post['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ["Invalid email address."];
    }

    $user = $this->findByEmail($email);

    // Only act if the account exists and is active,
    // but the caller never learns which case it was.
    if ($user && $user->isActive()) {
        // Remove older tokens of this user
        $del = $this->db->prepare('DELETE FROM password_resets WHERE user_id = :id');
        $del->execute(['id' => $user->getId()]);

        $token = bin2hex(random_bytes(32));
        $ins = $this->db->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at)
             VALUES (:uid, :hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
        );
        $ins->execute([
            'uid'  => $user->getId(),
            'hash' => hash('sha256', $token),
        ]);

        $cfg  = require __DIR__ . '/../config/mail.php';
        $link = $cfg['base_url'] . '?action=reset&token=' . $token;

        $this->sendMail(
            $user->getEmail(),
            'Reset your password',
            "Hello " . $user->getUsername() . ",\n\n"
            . "Click this link to choose a new password (valid for 1 hour):\n"
            . $link . "\n\n"
            . "If you did not ask for this, ignore this email."
        );
    }

    return []; // always the same result
}

/** Returns the user id if the token is valid and not expired, otherwise null */
public function validateResetToken(string $token): ?int
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }
    $stmt = $this->db->prepare(
        'SELECT user_id FROM password_resets
         WHERE token_hash = :hash AND expires_at > NOW() LIMIT 1'
    );
    $stmt->execute(['hash' => hash('sha256', $token)]);
    $id = $stmt->fetchColumn();
    return $id ? (int) $id : null;
}

public function resetPassword(string $token, array $post): array
{
    $errors = [];

    if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
        return ["Invalid security token. Please try again."];
    }

    $userId = $this->validateResetToken($token);
    if ($userId === null) {
        return ["This link is invalid or has expired."];
    }

    $password        = $post['password'] ?? '';
    $passwordConfirm = $post['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $passwordConfirm) {
        $errors[] = "Passwords do not match.";
    }
    if (!empty($errors)) {
        return $errors;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $upd = $this->db->prepare('UPDATE users SET password = :p WHERE id = :id');
    $upd->execute(['p' => $hashed, 'id' => $userId]);

    // The token can only be used once
    $del = $this->db->prepare('DELETE FROM password_resets WHERE user_id = :id');
    $del->execute(['id' => $userId]);

    return [];
}

}


