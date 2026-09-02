<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    csrf_verify();
    $id = (int) $_POST['delete_id'];

    $stmt = $pdo->prepare('SELECT featured_image FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if ($post) {
        $pdo->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([$id]);
        if ($post['featured_image']) {
            $filePath = UPLOAD_DIR_BLOG . '/' . basename($post['featured_image']);
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
        set_flash('success', 'Post deleted.');
    }
    redirect('blogs.php');
}

$posts = $pdo->query('SELECT * FROM blog_posts ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Blog Posts';
require __DIR__ . '/includes/header.php';
?>
<div class="admin-toolbar">
    <div></div>
    <a href="blog_form.php" class="admin-btn">+ Add Post</a>
</div>

<?php if (empty($posts)): ?>
    <div class="admin-empty">No blog posts yet. Add your first one above.</div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= e($post['title']) ?></td>
                    <td><span class="admin-badge admin-badge-<?= e($post['status']) ?>"><?= e(ucfirst($post['status'])) ?></span></td>
                    <td><?= e(date('d M Y', strtotime($post['updated_at']))) ?></td>
                    <td>
                        <a href="blog_form.php?id=<?= (int) $post['id'] ?>">Edit</a>
                        &nbsp;|&nbsp;
                        <form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this post?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= (int) $post['id'] ?>">
                            <button type="submit" style="all: unset; color: var(--secondary); cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
