<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$testimonial = ['customer_name' => '', 'rating' => 5, 'review_text' => '', 'is_published' => 1];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Testimonial not found.');
        redirect('testimonials.php');
    }
    $testimonial = $found;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $customerName = trim($_POST['customer_name'] ?? '');
    $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
    $reviewText = trim($_POST['review_text'] ?? '');
    $isPublished = isset($_POST['is_published']) ? 1 : 0;

    if ($customerName === '' || $reviewText === '') {
        $error = 'Customer name and review text are required.';
    } else {
        if ($id) {
            $pdo->prepare('UPDATE testimonials SET customer_name = ?, rating = ?, review_text = ?, is_published = ? WHERE id = ?')
                ->execute([$customerName, $rating, $reviewText, $isPublished, $id]);
            set_flash('success', 'Testimonial updated.');
        } else {
            $pdo->prepare('INSERT INTO testimonials (customer_name, rating, review_text, is_published) VALUES (?, ?, ?, ?)')
                ->execute([$customerName, $rating, $reviewText, $isPublished]);
            set_flash('success', 'Testimonial added.');
        }
        redirect('testimonials.php');
    }
}

$pageTitle = $id ? 'Edit Testimonial' : 'Add Testimonial';
require __DIR__ . '/includes/header.php';
?>
<form method="post" class="admin-form">
    <?= csrf_field() ?>
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <?php if ($error): ?>
        <div class="admin-flash admin-flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <label for="customer_name">Customer Name</label>
    <input type="text" id="customer_name" name="customer_name" value="<?= e($testimonial['customer_name']) ?>" required>

    <label for="rating">Rating</label>
    <select id="rating" name="rating">
        <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>" <?= (int) $testimonial['rating'] === $i ? 'selected' : '' ?>><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
        <?php endfor; ?>
    </select>

    <label for="review_text">Review Text</label>
    <textarea id="review_text" name="review_text" rows="5" required><?= e($testimonial['review_text']) ?></textarea>

    <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
        <input type="checkbox" name="is_published" value="1" style="width: auto;" <?= $testimonial['is_published'] ? 'checked' : '' ?>>
        Published (visible on the website)
    </label>

    <div class="admin-actions">
        <button type="submit" class="admin-btn"><?= $id ? 'Save Changes' : 'Add Testimonial' ?></button>
        <a href="testimonials.php" class="admin-btn admin-btn-secondary">Cancel</a>
    </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
