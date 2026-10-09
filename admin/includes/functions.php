<?php
/** Shared helper functions for the admin panel. */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Invalid or expired form submission. Please go back and try again.');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'post';
}

function unique_slug(PDO $pdo, string $baseSlug, ?int $excludeId = null): string
{
    $slug = $baseSlug;
    $suffix = 2;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM blog_posts WHERE slug = ? AND id != ?');
    while (true) {
        $stmt->execute([$slug, $excludeId ?? 0]);
        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $suffix;
        $suffix++;
    }
}

/**
 * Blog content saved before the rich-text editor existed is plain text with
 * no HTML at all, so line breaks the admin typed are just "\n" characters —
 * browsers collapse those to a single space, making everything run together
 * on one line. Content already containing block-level HTML (i.e. anything
 * saved by the rich-text editor) is left untouched.
 */
function normalize_legacy_content(?string $content): string
{
    $content = $content ?? '';
    if (trim($content) === '') {
        return '';
    }
    if (preg_match('/<(p|div|h[1-6]|ul|ol|li|blockquote|br)\b/i', $content)) {
        return $content;
    }

    $paragraphs = preg_split('/(?:\r\n|\n|\r){2,}/', trim($content));
    $html = array_map(function ($para) {
        return '<p>' . nl2br(e(trim($para))) . '</p>';
    }, $paragraphs);

    return implode('', $html);
}

/**
 * Strip inline style="..." attributes left over from pasting content in from
 * Word, Google Docs, or a webpage. Those carry layout that fights the site's
 * own CSS and bloats the saved HTML.
 */
function strip_inline_styles(string $content): string
{
    return preg_replace('/\s+style\s*=\s*(".*?"|\'.*?\')/is', '', $content);
}

/**
 * The rich-text editor's toolbar only offers H2-H4, but older posts (or
 * content pasted in from elsewhere) can still carry H1 tags from section
 * headings. A page must only have one H1 — the post title — so any H1 inside
 * the article body is downgraded to an H2.
 */
function downgrade_h1_tags(string $content): string
{
    $content = preg_replace('/<h1(\s[^>]*)?>/i', '<h2$1>', $content);
    return preg_replace('/<\/h1>/i', '</h2>', $content);
}

/**
 * Full normalization applied to a post's content before it is rendered on
 * the public site: upgrades legacy plain-text posts to HTML, strips any
 * inline styles, and downgrades any stray H1s to H2s.
 */
function prepare_post_content(?string $content): string
{
    $content = normalize_legacy_content($content);
    $content = strip_inline_styles($content);
    return downgrade_h1_tags($content);
}

/**
 * Decode a blog post's faq_json column into a clean list of
 * ['question' => ..., 'answer' => ...] pairs, dropping any entry missing
 * either side. Used both to re-populate the admin FAQ builder when editing a
 * post and to render the public FAQ section and FAQPage schema.
 */
function decode_faq_json(?string $json): array
{
    if (!$json) {
        return [];
    }
    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        return [];
    }
    $faqs = [];
    foreach ($decoded as $item) {
        $question = trim((string) ($item['question'] ?? ''));
        $answer = trim((string) ($item['answer'] ?? ''));
        if ($question !== '' && $answer !== '') {
            $faqs[] = ['question' => $question, 'answer' => $answer];
        }
    }
    return $faqs;
}

/**
 * Validate and move an uploaded image into $destDir.
 * Returns the stored filename on success, or null if no file was uploaded.
 * Dies with an error message if the upload is invalid.
 */
function handle_image_upload(string $fieldName, string $destDir): ?string
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Image upload failed (error code ' . (int) $file['error'] . ').');
    }

    $maxBytes = 5 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        die('Image is too large. Maximum size is 5MB.');
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        die('Unsupported image type. Allowed: JPG, PNG, WEBP, GIF.');
    }

    if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
        die('Could not create upload directory.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $destPath = rtrim($destDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        die('Could not save uploaded image.');
    }

    return $filename;
}
