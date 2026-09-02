-- Blacktown Fridge Repairs — Admin Panel database schema
-- Import this into a MySQL/MariaDB database before using the admin panel.
-- After importing, log in with username "admin" / password "ChangeMe123!"
-- and change the password immediately from the admin panel.

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NOT NULL DEFAULT '',
    orientation ENUM('landscape', 'portrait') NOT NULL DEFAULT 'portrait',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(255),
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
    review_text TEXT NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin account — username: admin / password: ChangeMe123!
-- CHANGE THIS PASSWORD IMMEDIATELY after your first login.
INSERT INTO admins (username, password_hash) VALUES
    ('admin', '$2y$12$rQNh0xrZzyjZSFJIBvRcPeqE33fvfOKfzfSOEjncecHyArxf1Ik7W')
ON DUPLICATE KEY UPDATE username = username;
