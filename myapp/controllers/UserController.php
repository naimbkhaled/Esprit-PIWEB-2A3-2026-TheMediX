<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/AuthController.php';

class UserController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    private function existsForOtherUser(string $email, string $username, int $id): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM users WHERE (email = :email OR username = :username) AND id <> :id LIMIT 1'
        );
        $stmt->execute(['email' => $email, 'username' => $username, 'id' => $id]);
        return (bool) $stmt->fetch();
    }

    private function updateUser(UserModel $user): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id'
        );
        return $stmt->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'id' => $user->getId(),
        ]);
    }

    public function updateProfile(int $id, array $post): array
    {
        $errors = [];

        if (!AuthController::verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors[] = "Invalid security token. Please try again.";
            return $errors;
        }

        $user = $this->findById($id);
        if (!$user) {
            $errors[] = "Account not found.";
            return $errors;
        }

        $username = trim($post['username'] ?? '');
        $email = trim($post['email'] ?? '');
        $currentPassword = $post['current_password'] ?? '';
        $newPassword = $post['new_password'] ?? '';
        $newPasswordConfirm = $post['new_password_confirm'] ?? '';

        if ($username === '' || $email === '') {
            $errors[] = "Username and email are required.";
        }
        if (!preg_match('/^[a-zA-Z0-9_\-]{3,50}$/', $username)) {
            $errors[] = "Username must be 3 to 50 characters (letters, digits, _ or -).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
            $errors[] = "Invalid email address.";
        }

        $changePassword = $newPassword !== '';
        if ($changePassword) {
            if (strlen($newPassword) < 8) {
                $errors[] = "New password must be at least 8 characters long.";
            }
            if ($newPassword !== $newPasswordConfirm) {
                $errors[] = "New passwords do not match.";
            }
            if (!password_verify($currentPassword, $user->getPassword())) {
                $errors[] = "Current password is incorrect.";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        if ($this->existsForOtherUser($email, $username, $id)) {
            $errors[] = "This email or username is already taken.";
            return $errors;
        }

        $user->setUsername($username);
        $user->setEmail($email);
        if ($changePassword) {
            $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
        }

        if (!$this->updateUser($user)) {
            $errors[] = "An error occurred while saving your information.";
            return $errors;
        }

        $_SESSION['username'] = $user->getUsername();

        return [];
    }
}
