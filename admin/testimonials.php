<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    csrf_verify();
    $id = (int) $_POST['delete_id'];
    $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
    set_flash('success', 'Testimonial deleted.');
    redirect('testimonials.php');
}

$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Testimonials';
require __DIR__ . '/includes/header.php';
?>
<div class="admin-toolbar">
    <div></div>
    <a href="testimonial_form.php" class="admin-btn">+ Add Testimonial</a>
</div>

<?php if (empty($testimonials)): ?>
    <div class="admin-empty">No testimonials yet. Add your first one above.</div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Published</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($testimonials as $t): ?>
                <tr>
                    <td><?= e($t['customer_name']) ?></td>
                    <td><?= str_repeat('★', (int) $t['rating']) . str_repeat('☆', 5 - (int) $t['rating']) ?></td>
                    <td><?= e(mb_strimwidth($t['review_text'], 0, 80, '…')) ?></td>
                    <td><?= $t['is_published'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <a href="testimonial_form.php?id=<?= (int) $t['id'] ?>">Edit</a>
                        &nbsp;|&nbsp;
                        <form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this testimonial?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= (int) $t['id'] ?>">
                            <button type="submit" style="all: unset; color: var(--secondary); cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
