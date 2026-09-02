<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$galleryCount = (int) $pdo->query('SELECT COUNT(*) FROM gallery_images')->fetchColumn();
$blogCount = (int) $pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
$publishedBlogCount = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
$testimonialCount = (int) $pdo->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
    <div class="admin-form" style="max-width: none;">
        <div style="color: var(--text-light); font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.05em;">Gallery Images</div>
        <div style="font-size: 2.2em; font-weight: 700; color: var(--primary);"><?= $galleryCount ?></div>
        <a href="gallery.php">Manage gallery &rarr;</a>
    </div>
    <div class="admin-form" style="max-width: none;">
        <div style="color: var(--text-light); font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.05em;">Blog Posts</div>
        <div style="font-size: 2.2em; font-weight: 700; color: var(--primary);"><?= $blogCount ?></div>
        <div class="admin-hint"><?= $publishedBlogCount ?> published</div>
        <a href="blogs.php">Manage posts &rarr;</a>
    </div>
    <div class="admin-form" style="max-width: none;">
        <div style="color: var(--text-light); font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.05em;">Testimonials</div>
        <div style="font-size: 2.2em; font-weight: 700; color: var(--primary);"><?= $testimonialCount ?></div>
        <a href="testimonials.php">Manage testimonials &rarr;</a>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
