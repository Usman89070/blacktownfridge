# Admin Panel

A self-contained PHP + MySQL admin panel for managing the **gallery**, **blog posts**, and **testimonials** shown on the main site. It is independent of the WordPress CMS at `/cms/` — nothing here talks to WordPress.

## Setup

1. **Create a MySQL database** on your host and import the schema:
   ```
   mysql -u your_user -p your_database < admin/database/schema.sql
   ```
   This creates the `admins`, `gallery_images`, `blog_posts`, and `testimonials` tables, and seeds one login:
   - Username: `admin`
   - Password: `ChangeMe123!`

2. **Fill in your database credentials** in `admin/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Upload the `admin/` folder** to your server (it can sit alongside `index.html`, e.g. `yoursite.com/admin/`).

4. Make sure `admin/uploads/gallery/` and `admin/uploads/blog/` are **writable** by PHP (typically `chmod 755` is enough on shared hosting).

5. Visit `yoursite.com/admin/login.php`, log in with the seeded credentials above, and **change the password immediately** from the "Change Password" page.

## What it does

- **Gallery** — add/edit/delete images with alt text, orientation (landscape/portrait), and sort order.
- **Blog Posts** — title, URL slug (auto-generated from the title if left blank), excerpt, HTML content, featured image, and draft/published status.
- **Testimonials** — customer name, star rating, review text, and a published toggle.

## Important: this does not automatically update the live site

This admin panel manages its **own** database tables. The static `index.html` gallery grid and the WordPress-powered blog on the main site do **not** currently read from these tables — they're separate systems. To have the site actually display what you manage here, one of the following is needed next:

- Replace the static gallery `<img>` tags in `index.html` and the WordPress blog fetch in `blog.html`/`index.html` with PHP that queries these new tables, **or**
- Keep this panel as a separate, standalone content source and wire it up when you're ready.

Ask for this follow-up step explicitly if you'd like the homepage/blog to pull from this new database.

## Security notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- All database queries use prepared statements (PDO) to prevent SQL injection.
- All state-changing forms (login, add/edit/delete) are protected with CSRF tokens.
- Uploaded files are validated by real MIME type (not just extension), capped at 5MB, and renamed to random filenames on save.
- The `admin/uploads/` and `admin/database/` folders block direct PHP execution and file access via `.htaccess` (Apache only — if your host runs Nginx, add equivalent rules).
- **Never commit real database credentials** to a public Git repository. `admin/config.php` ships with placeholder values on purpose.
