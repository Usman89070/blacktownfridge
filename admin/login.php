<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both a username and password.';
    } elseif (attempt_login($pdo, $username, $password)) {
        redirect('index.php');
    } else {
        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Login</title>
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-login-page">
    <div class="admin-login-box">
        <h1>Admin Login</h1>
        <?php if ($error): ?>
            <div class="admin-flash admin-flash-error" style="margin: 0 0 15px;"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" novalidate>
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <div class="admin-actions" style="margin-top: 20px;">
                <button type="submit" class="admin-btn" style="width: 100%;">Log In</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
