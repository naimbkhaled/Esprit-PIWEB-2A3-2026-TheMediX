<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_start();

$sessionLifetime = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'");

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';

$authController = new AuthController();
$providers = $authController->getProviders();
$authController->loginFromCookie();
$userController = new UserController();

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard'));
            exit;
        }
        header('Location: index.php?action=login');
        exit;

    case 'register':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard'));
            exit;
        }
        $errors = [];
        $old = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->register($_POST, $_FILES['preuve_identite'] ?? null);
            $old = $_POST;
            if (empty($errors)) {
                header('Location: index.php?action=login&registered=1');
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/gestion_users/view/front/register.php';
        break;

    case 'login':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard'));
            exit;
        }
        $errors = [];
        $old = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->login($_POST);
            $old = ['email' => $_POST['email'] ?? ''];
            if (empty($errors)) {
                $redirect = ($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard';
                header('Location: index.php?action=' . $redirect);
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/views/login.php';
        break;

    case 'oauth':
        $provider = is_string($_GET['provider'] ?? null) ? $_GET['provider'] : '';
        $errors = $authController->oauthStart($provider);
        $old = [];
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/views/login.php';
        break;

    case 'oauth_callback':
        $errors = $authController->oauthCallback($_GET);
        if (empty($errors)) {
            header('Location: ' . AuthController::homeUrl());
            exit;
        }
        $old = [];
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/views/login.php';
        break;

    case 'forgot':
        if (AuthController::isAuthenticated()) {
            header('Location: ' . AuthController::homeUrl());
            exit;
        }
        $errors = [];
        $old = [];
        $sent = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->requestReset($_POST);
            $old = ['email' => $_POST['email'] ?? ''];
            $sent = empty($errors);
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/views/forgot.php';
        break;

    case 'reset':
        $token = is_string($_REQUEST['token'] ?? null) ? $_REQUEST['token'] : '';
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->resetPassword($_POST);
            if (empty($errors)) {
                header('Location: index.php?action=login&reset=1');
                exit;
            }
        }
        $valid = $authController->isResetTokenValid($token);
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/views/reset.php';
        break;

    case 'logout':
        $authController->logout();
        header('Location: index.php?action=login');
        exit;

    case 'deactivate':
        AuthController::requireAuth();
        if (($_SESSION['role'] ?? '') === 'admin') {
            http_response_code(403);
            echo 'Administrator accounts cannot be deactivated here.';
            break;
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->deactivateAccount((int) $_SESSION['user_id'], $_POST);
            if (empty($errors)) {
                $authController->logout();
                header('Location: index.php?action=login&deactivated=1');
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/testttttttttttttttttttt/views/desactivate.php';
        break;

    case 'dashboard':
        AuthController::requireAuth();
        $user = $authController->findById((int) $_SESSION['user_id']);
        if (!$user) {
            $authController->logout();
            header('Location: index.php?action=login');
            exit;
        }
        require __DIR__ . '/views/user_space.php';
        break;

    case 'admin':
        AuthController::requireRole('admin');
        $user = $authController->findById((int) $_SESSION['user_id']);
        if (!$user) {
            $authController->logout();
            header('Location: index.php?action=login');
            exit;
        }
        $allUsers = $authController->getAllUsers();
        $totalUsers = $authController->countAllUsers();
        $totalAdmins = $authController->countByRole('admin');
        $totalRegular = $authController->countByRole('user');
        require __DIR__ . '/views/admin_space.php';
        break;

    case 'profile':
        AuthController::requireAuth();
        $user = $userController->findById((int) $_SESSION['user_id']);
        if (!$user) {
            $authController->logout();
            header('Location: index.php?action=login');
            exit;
        }
        require __DIR__ . '/views/profile_view.php';
        break;

    case 'profile_edit':
        AuthController::requireAuth();
        $user = $userController->findById((int) $_SESSION['user_id']);
        if (!$user) {
            $authController->logout();
            header('Location: index.php?action=login');
            exit;
        }
        $errors = [];
        $old = ['username' => $user->getUsername(), 'email' => $user->getEmail()];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $userController->updateProfile($user->getId(), $_POST);
            if (empty($errors)) {
                header('Location: index.php?action=profile&updated=1');
                exit;
            }
            $old = ['username' => $_POST['username'] ?? '', 'email' => $_POST['email'] ?? ''];
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/views/profile_edit.php';
        break;

    default:
        http_response_code(404);
        echo '404 - Page not found';
        break;
}
