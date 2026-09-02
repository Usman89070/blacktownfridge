<?php
/** Authentication helpers for the admin panel. Requires config.php + functions.php to be loaded first. */

function current_admin_id(): ?int
{
    return $_SESSION['admin_id'] ?? null;
}

function is_logged_in(): bool
{
    return current_admin_id() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function attempt_login(PDO $pdo, string $username, string $password): bool
{
    $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        // Constant-time-ish delay to slow down brute forcing.
        usleep(300000);
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_username'] = $username;

    return true;
}

function do_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
