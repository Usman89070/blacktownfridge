<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$post = ['title' => '', 'slug' => '', 'excerpt' => '', 'meta_description' => '', 'author_byline' => '', 'content' => '', 'faq_json' => '[]', 'featured_image' => null, 'status' => 'draft'];

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
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $authorByline = trim($_POST['author_byline'] ?? '');
    $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    $customSlug = trim($_POST['slug'] ?? '');

    // An "Advanced HTML" paste, when filled in, replaces whatever the rich-text
    // editor above produced — this is the escape hatch for content the Quill
    // toolbar can't build itself (tables, callout boxes, embedded schema).
    $rawHtml = trim($_POST['raw_html'] ?? '');
    $contentSource = $rawHtml !== '' ? $rawHtml : ($_POST['content'] ?? '');
    $content = strip_inline_styles(downgrade_h1_tags($contentSource));

    $faqs = [];
    $postedFaqJson = $_POST['faq_json'] ?? '[]';
    $decodedFaqs = json_decode($postedFaqJson, true);
    if (is_array($decodedFaqs)) {
        foreach ($decodedFaqs as $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));
            if ($question !== '' && $answer !== '') {
                $faqs[] = ['question' => $question, 'answer' => $answer];
            }
        }
    }
    $faqJson = $faqs ? json_encode($faqs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

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
                $pdo->prepare('UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, meta_description = ?, author_byline = ?, content = ?, faq_json = ?, featured_image = ?, status = ? WHERE id = ?')
                    ->execute([$title, $slug, $excerpt, $metaDescription, $authorByline, $content, $faqJson, $uploadedFilename, $status, $id]);
            } else {
                $pdo->prepare('UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, meta_description = ?, author_byline = ?, content = ?, faq_json = ?, status = ? WHERE id = ?')
                    ->execute([$title, $slug, $excerpt, $metaDescription, $authorByline, $content, $faqJson, $status, $id]);
            }
            set_flash('success', 'Post updated.');
        } else {
            $pdo->prepare('INSERT INTO blog_posts (title, slug, excerpt, meta_description, author_byline, content, faq_json, featured_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute([$title, $slug, $excerpt, $metaDescription, $authorByline, $content, $faqJson, $uploadedFilename, $status]);
            set_flash('success', 'Post created.');
        }
        redirect('blogs.php');
    }
}

$existingFaqs = decode_faq_json($post['faq_json'] ?? null);

$pageTitle = $id ? 'Edit Post' : 'Add Post';
$extraHead = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.snow.min.css">';
require __DIR__ . '/includes/header.php';
?>
<form id="postForm" method="post" enctype="multipart/form-data" class="admin-form" style="max-width: 760px;">
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

    <label for="meta_description">Meta Description (for Google search results)</label>
    <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"><?= e($post['meta_description']) ?></textarea>
    <div class="admin-hint">Shown in Google search results. Keep it under 160 characters. If left empty, the excerpt (or the start of the article) is used instead.</div>

    <label for="author_byline">Author Byline (optional)</label>
    <input type="text" id="author_byline" name="author_byline" value="<?= e($post['author_byline'] ?? '') ?>" placeholder="e.g. Written by [Technician Name] &mdash; 30 years repairing fridges in Western Sydney">
    <div class="admin-hint">Shown under the title on the published post. Leave empty to hide it.</div>

    <label for="content-editor">Content</label>
    <div id="content-editor"><?= normalize_legacy_content($post['content']) ?></div>
    <textarea id="content" name="content" style="display:none;"></textarea>
    <div class="admin-hint">Use the toolbar for bold, headings, lists and links. Pasting from Word, Google Docs, or a webpage keeps that formatting. Press Enter for a new paragraph, or Shift+Enter for a line break within one.</div>

    <label for="raw_html">Advanced HTML (optional &mdash; overrides the editor above)</label>
    <textarea id="raw_html" name="raw_html" rows="8" placeholder="Paste ready-made HTML here (tables, callout boxes, embedded schema, etc.). If this box has anything in it when you save, it replaces whatever is in the editor above."></textarea>
    <div class="admin-hint">Leave this empty to just use the editor above. Fill it in only when you've been given finished HTML to paste in (for things the editor can't build, like tables).</div>

    <label>Frequently Asked Questions (optional)</label>
    <div class="admin-hint">Add question/answer pairs to show an FAQ section on the post and generate FAQPage search schema automatically.</div>
    <div id="faq-builder"></div>
    <button type="button" id="faq-add-btn" class="admin-btn admin-btn-secondary" style="margin-top: 10px;">+ Add FAQ</button>
    <input type="hidden" id="faq_json" name="faq_json" value="[]">

    <label for="featured_image" style="margin-top: 20px;">Featured Image <?= $id ? '(leave empty to keep the current image)' : '' ?></label>
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

<style>
    .faq-row { display: flex; flex-direction: column; gap: 8px; padding: 14px; margin-top: 12px; border: 1px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; position: relative; }
    .faq-row input, .faq-row textarea { width: 100%; box-sizing: border-box; }
    .faq-row-remove { align-self: flex-end; background: none; border: none; color: #C1121F; font-weight: 600; cursor: pointer; font-size: 0.85em; padding: 2px 6px; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js"></script>
<script>
    const quill = new Quill('#content-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'link'],
                ['clean']
            ]
        }
    });

    // --- FAQ builder ---
    const faqBuilder = document.getElementById('faq-builder');
    const faqJsonField = document.getElementById('faq_json');
    const initialFaqs = <?= json_encode($existingFaqs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    function addFaqRow(question, answer) {
        const row = document.createElement('div');
        row.className = 'faq-row';
        row.innerHTML =
            '<input type="text" class="faq-question" placeholder="Question">' +
            '<textarea class="faq-answer" rows="2" placeholder="Answer"></textarea>' +
            '<button type="button" class="faq-row-remove">Remove</button>';
        row.querySelector('.faq-question').value = question || '';
        row.querySelector('.faq-answer').value = answer || '';
        row.querySelector('.faq-row-remove').addEventListener('click', function () {
            row.remove();
        });
        faqBuilder.appendChild(row);
    }

    if (initialFaqs.length) {
        initialFaqs.forEach(function (faq) { addFaqRow(faq.question, faq.answer); });
    }

    document.getElementById('faq-add-btn').addEventListener('click', function () {
        addFaqRow('', '');
    });

    document.getElementById('postForm').addEventListener('submit', function () {
        document.getElementById('content').value = quill.root.innerHTML;

        const faqs = [];
        faqBuilder.querySelectorAll('.faq-row').forEach(function (row) {
            const question = row.querySelector('.faq-question').value.trim();
            const answer = row.querySelector('.faq-answer').value.trim();
            if (question && answer) {
                faqs.push({ question: question, answer: answer });
            }
        });
        faqJsonField.value = JSON.stringify(faqs);
    });
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
