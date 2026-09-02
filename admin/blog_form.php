<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$post = ['title' => '', 'slug' => '', 'excerpt' => '', 'content' => '', 'featured_image' => null, 'status' => 'draft'];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Post not found.');
        redirect('blogs.php');
    }
    $post = $found;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    $customSlug = trim($_POST['slug'] ?? '');

    $uploadedFilename = handle_image_upload('featured_image', UPLOAD_DIR_BLOG);

    if ($title === '') {
        $error = 'Title is required.';
    } else {
        $baseSlug = slugify($customSlug !== '' ? $customSlug : $title);
        $slug = unique_slug($pdo, $baseSlug, $id);

        if ($id) {
            if ($uploadedFilename) {
                if ($post['featured_image']) {
                    $old = UPLOAD_DIR_BLOG . '/' . basename($post['featured_image']);
                    if (is_file($old)) {
                        unlink($old);
                    }
                }
                $pdo->prepare('UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, status = ? WHERE id = ?')
                    ->execute([$title, $slug, $excerpt, $content, $uploadedFilename, $status, $id]);
            } else {
                $pdo->prepare('UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, status = ? WHERE id = ?')
                    ->execute([$title, $slug, $excerpt, $content, $status, $id]);
            }
            set_flash('success', 'Post updated.');
        } else {
            $pdo->prepare('INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, status) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$title, $slug, $excerpt, $content, $uploadedFilename, $status]);
            set_flash('success', 'Post created.');
        }
        redirect('blogs.php');
    }
}

$pageTitle = $id ? 'Edit Post' : 'Add Post';
require __DIR__ . '/includes/header.php';
?>
<form method="post" enctype="multipart/form-data" class="admin-form" style="max-width: 760px;">
    <?= csrf_field() ?>
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <?php if ($error): ?>
        <div class="admin-flash admin-flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($post['title']) ?>" required>

    <label for="slug">URL Slug</label>
    <input type="text" id="slug" name="slug" value="<?= e($post['slug']) ?>" placeholder="Leave empty to generate from title">

    <label for="excerpt">Excerpt</label>
    <textarea id="excerpt" name="excerpt" rows="3"><?= e($post['excerpt']) ?></textarea>

    <label for="content">Content</label>
    <textarea id="content" name="content" rows="14"><?= e($post['content']) ?></textarea>
    <div class="admin-hint">Basic HTML tags (e.g. &lt;p&gt;, &lt;strong&gt;, &lt;a&gt;) are allowed here.</div>

    <label for="featured_image">Featured Image <?= $id ? '(leave empty to keep the current image)' : '' ?></label>
    <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif">
    <?php if ($id && $post['featured_image']): ?>
        <img class="admin-preview" src="<?= e(UPLOAD_URL_BLOG . '/' . $post['featured_image']) ?>" alt="">
    <?php endif; ?>

    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
    </select>

    <div class="admin-actions">
        <button type="submit" class="admin-btn"><?= $id ? 'Save Changes' : 'Create Post' ?></button>
        <a href="blogs.php" class="admin-btn admin-btn-secondary">Cancel</a>
    </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
