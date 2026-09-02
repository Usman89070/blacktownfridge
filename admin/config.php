<?php
/**
 * Admin panel configuration.
 * Fill in your real database credentials below before deploying.
 * Never commit real production credentials to a public repository.
 */

// ---- Database connection settings (fill these in) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'CHANGE_ME_db_name');
define('DB_USER', 'CHANGE_ME_db_user');
define('DB_PASS', 'CHANGE_ME_db_password');

// ---- Paths ----
define('ADMIN_ROOT', __DIR__);
define('UPLOAD_DIR_GALLERY', ADMIN_ROOT . '/uploads/gallery');
define('UPLOAD_DIR_BLOG', ADMIN_ROOT . '/uploads/blog');
define('UPLOAD_URL_GALLERY', 'uploads/gallery');
define('UPLOAD_URL_BLOG', 'uploads/blog');

// ---- Session ----
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

// ---- Database connection (PDO) ----
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Check admin/config.php credentials.');
}
