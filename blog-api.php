<?php
/**
 * Public read-only JSON API for published blog posts.
 * Backed by the same database as admin/blog_form.php.
 *
 * GET blog-api.php              -> list of published posts (newest first)
 * GET blog-api.php?limit=5      -> limit how many are returned (default 50, max 50)
 * GET blog-api.php?slug=my-post -> a single published post by slug
 */
require __DIR__ . '/admin/config.php';
require __DIR__ . '/admin/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

function post_image_url(?string $filename): ?string
{
    return $filename ? '/admin/' . UPLOAD_URL_BLOG . '/' . $filename : null;
}

function format_post(array $post): array
{
    return [
        'id' => (int) $post['id'],
        'title' => $post['title'],
        'slug' => $post['slug'],
        'excerpt' => $post['excerpt'],
        'content' => normalize_legacy_content($post['content']),
        'image' => post_image_url($post['featured_image']),
        'date' => date('c', strtotime($post['created_at'])),
    ];
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

if ($slug !== null && $slug !== '') {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();

    if (!$post) {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found']);
        exit;
    }

    echo json_encode(format_post($post));
    exit;
}

$limit = isset($_GET['limit']) ? max(1, min(50, (int) $_GET['limit'])) : 50;

$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT :limit");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

echo json_encode(array_map('format_post', $stmt->fetchAll()));
