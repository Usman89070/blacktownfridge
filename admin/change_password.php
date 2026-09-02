<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $stmt = $pdo->prepare('SELECT password_hash FROM admins WHERE id = ?');
    $stmt->execute([current_admin_id()]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($currentPassword, $admin['password_hash'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'New password must be at least 8 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirmation do not match.';
    } else {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([$newHash, current_admin_id()]);
        set_flash('success', 'Password updated successfully.');
        redirect('change_password.php');
    }
}

$pageTitle = 'Change Password';
require __DIR__ . '/includes/header.php';
?>
<form method="post" class="admin-form">
    <?= csrf_field() ?>

    <?php if ($error): ?>
        <div class="admin-flash admin-flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <label for="current_password">Current Password</label>
    <input type="password" id="current_password" name="current_password" required>

    <label for="new_password">New Password</label>
    <input type="password" id="new_password" name="new_password" required minlength="8">

    <label for="confirm_password">Confirm New Password</label>
    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">

    <div class="admin-actions">
        <button type="submit" class="admin-btn">Update Password</button>
    </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
