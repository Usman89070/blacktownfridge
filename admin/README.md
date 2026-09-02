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

2. **(Optional) Import the site's existing gallery photos and reviews** so you don't have to re-enter them by hand:
   ```
   mysql -u your_user -p your_database < admin/database/seed_existing_content.sql
   ```
   This adds the 6 "Our Recent Work" photos and the 5 customer reviews that were already hardcoded into `index.html`. The matching image files are already sitting in `admin/uploads/gallery/`, so once this import runs they'll show up immediately in `admin/gallery.php`.

   Note: the original review cards on the site never captured a customer name — only a 5-star rating and the quote — so all 5 are seeded as **"Verified Customer"**. Edit each one in `admin/testimonial_form.php` to add the real name if you have it on file.

3. **Fill in your database credentials** in `admin/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **Upload the `admin/` folder** to your server (it can sit alongside `index.html`, e.g. `yoursite.com/admin/`).

5. Make sure `admin/uploads/gallery/` and `admin/uploads/blog/` are **writable** by PHP (typically `chmod 755` is enough on shared hosting).

6. Visit `yoursite.com/admin/login.php`, log in with the seeded credentials above, and **change the password immediately** from the "Change Password" page.

## What it does

- **Gallery** — add/edit/delete images with alt text, orientation (landscape/portrait), and sort order.
- **Blog Posts** — title, URL slug (auto-generated from the title if left blank), excerpt, HTML content, featured image, and draft/published status.
- **Testimonials** — customer name, star rating, review text, and a published toggle.

## How blog posts reach the site

Published blog posts are served through `blog-api.php` at the repo root — a small public JSON endpoint that reads from the same `blog_posts` table (published posts only; drafts never leave the admin panel):

- `blog-api.php` — list of published posts, newest first
- `blog-api.php?limit=3` — limit how many are returned
- `blog-api.php?slug=your-post-slug` — a single post by slug (404 if it's a draft or doesn't exist)

`blog.html`, `index.html` (homepage preview), and `single-post.html` all fetch from this endpoint, so any post you mark **Published** in `admin/blog_form.php` appears on the live site immediately — no separate publish step. This replaced the old WordPress REST API fetch, so the WordPress CMS at `/cms/` is no longer used by these pages.

## Gallery images

The **gallery you manage in `/admin` is separate** from the "Our Recent Work" grid on the homepage, which is still a hand-written list of `<img>` tags in `index.html`. Images added via `admin/gallery_form.php` are stored and listed inside the admin panel, but won't appear on the homepage automatically. Ask if you'd like the homepage gallery switched over to read from this table too, the same way blog posts now do.

## Security notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- All database queries use prepared statements (PDO) to prevent SQL injection.
- All state-changing forms (login, add/edit/delete) are protected with CSRF tokens.
- Uploaded files are validated by real MIME type (not just extension), capped at 5MB, and renamed to random filenames on save.
- The `admin/uploads/` and `admin/database/` folders block direct PHP execution and file access via `.htaccess` (Apache only — if your host runs Nginx, add equivalent rules).
- **Never commit real database credentials** to a public Git repository. `admin/config.php` ships with placeholder values on purpose.
