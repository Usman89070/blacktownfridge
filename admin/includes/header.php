<?php
/** Shared admin layout header. Expects $pageTitle to be set. Requires config/functions/auth already loaded and require_login() already called. */
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle ?? 'Admin') ?> | Site Admin</title>
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand">Site Admin</div>
        <nav>
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Dashboard</a>
            <a href="gallery.php" class="<?= basename($_SERVER['PHP_SELF']) === 'gallery.php' ? 'active' : '' ?>">Gallery</a>
            <a href="blogs.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['blogs.php', 'blog_form.php']) ? 'active' : '' ?>">Blog Posts</a>
            <a href="testimonials.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['testimonials.php', 'testimonial_form.php']) ? 'active' : '' ?>">Testimonials</a>
            <a href="change_password.php" class="<?= basename($_SERVER['PHP_SELF']) === 'change_password.php' ? 'active' : '' ?>">Change Password</a>
        </nav>
        <form action="logout.php" method="post" class="admin-logout-form">
            <?= csrf_field() ?>
            <button type="submit" class="admin-link-btn">Log Out</button>
        </form>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <h1><?= e($pageTitle ?? '') ?></h1>
            <span class="admin-user">Signed in as <?= e($_SESSION['admin_username'] ?? '') ?></span>
        </header>
        <?php if ($flash): ?>
            <div class="admin-flash admin-flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="admin-content">
