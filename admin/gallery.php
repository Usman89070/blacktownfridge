<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    csrf_verify();
    $id = (int) $_POST['delete_id'];

    $stmt = $pdo->prepare('SELECT file_path FROM gallery_images WHERE id = ?');
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if ($image) {
        $pdo->prepare('DELETE FROM gallery_images WHERE id = ?')->execute([$id]);
        $filePath = UPLOAD_DIR_GALLERY . '/' . basename($image['file_path']);
        if (is_file($filePath)) {
            unlink($filePath);
        }
        set_flash('success', 'Image deleted.');
    }
    redirect('gallery.php');
}

$images = $pdo->query('SELECT * FROM gallery_images ORDER BY sort_order ASC, created_at DESC')->fetchAll();

$pageTitle = 'Gallery';
require __DIR__ . '/includes/header.php';
?>
<div class="admin-toolbar">
    <div></div>
    <a href="gallery_form.php" class="admin-btn">+ Add Image</a>
</div>

<?php if (empty($images)): ?>
    <div class="admin-empty">No gallery images yet. Add your first one above.</div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Preview</th>
                <th>Alt Text</th>
                <th>Orientation</th>
                <th>Sort Order</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($images as $image): ?>
                <tr>
                    <td><img class="admin-thumb" src="<?= e(UPLOAD_URL_GALLERY . '/' . $image['file_path']) ?>" alt=""></td>
                    <td><?= e($image['alt_text']) ?></td>
                    <td><?= e(ucfirst($image['orientation'])) ?></td>
                    <td><?= (int) $image['sort_order'] ?></td>
                    <td>
                        <a href="gallery_form.php?id=<?= (int) $image['id'] ?>">Edit</a>
                        &nbsp;|&nbsp;
                        <form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this image?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= (int) $image['id'] ?>">
                            <button type="submit" class="admin-link-btn" style="all: unset; color: var(--secondary); cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
