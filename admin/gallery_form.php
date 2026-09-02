<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$image = ['alt_text' => '', 'orientation' => 'portrait', 'sort_order' => 0, 'file_path' => null];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM gallery_images WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Image not found.');
        redirect('gallery.php');
    }
    $image = $found;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $altText = trim($_POST['alt_text'] ?? '');
    $orientation = ($_POST['orientation'] ?? 'portrait') === 'landscape' ? 'landscape' : 'portrait';
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    $uploadedFilename = handle_image_upload('image', UPLOAD_DIR_GALLERY);

    if (!$id && !$uploadedFilename) {
        $error = 'Please choose an image to upload.';
    } else {
        if ($id) {
            if ($uploadedFilename) {
                $old = UPLOAD_DIR_GALLERY . '/' . basename($image['file_path']);
                if (is_file($old)) {
                    unlink($old);
                }
                $pdo->prepare('UPDATE gallery_images SET file_path = ?, alt_text = ?, orientation = ?, sort_order = ? WHERE id = ?')
                    ->execute([$uploadedFilename, $altText, $orientation, $sortOrder, $id]);
            } else {
                $pdo->prepare('UPDATE gallery_images SET alt_text = ?, orientation = ?, sort_order = ? WHERE id = ?')
                    ->execute([$altText, $orientation, $sortOrder, $id]);
            }
            set_flash('success', 'Image updated.');
        } else {
            $pdo->prepare('INSERT INTO gallery_images (file_path, alt_text, orientation, sort_order) VALUES (?, ?, ?, ?)')
                ->execute([$uploadedFilename, $altText, $orientation, $sortOrder]);
            set_flash('success', 'Image added.');
        }
        redirect('gallery.php');
    }
}

$pageTitle = $id ? 'Edit Gallery Image' : 'Add Gallery Image';
require __DIR__ . '/includes/header.php';
?>
<form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <?php if ($error): ?>
        <div class="admin-flash admin-flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <label for="image">Image <?= $id ? '(leave empty to keep the current image)' : '' ?></label>
    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif" <?= $id ? '' : 'required' ?>>
    <?php if ($id && $image['file_path']): ?>
        <img class="admin-preview" src="<?= e(UPLOAD_URL_GALLERY . '/' . $image['file_path']) ?>" alt="">
    <?php endif; ?>

    <label for="alt_text">Alt Text</label>
    <input type="text" id="alt_text" name="alt_text" value="<?= e($image['alt_text']) ?>" placeholder="Describe the image for accessibility">

    <label for="orientation">Orientation</label>
    <select id="orientation" name="orientation">
        <option value="portrait" <?= $image['orientation'] === 'portrait' ? 'selected' : '' ?>>Portrait</option>
        <option value="landscape" <?= $image['orientation'] === 'landscape' ? 'selected' : '' ?>>Landscape</option>
    </select>

    <label for="sort_order">Sort Order</label>
    <input type="number" id="sort_order" name="sort_order" value="<?= (int) $image['sort_order'] ?>">
    <div class="admin-hint">Lower numbers appear first in the gallery.</div>

    <div class="admin-actions">
        <button type="submit" class="admin-btn"><?= $id ? 'Save Changes' : 'Add Image' ?></button>
        <a href="gallery.php" class="admin-btn admin-btn-secondary">Cancel</a>
    </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
