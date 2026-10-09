-- Migration: add author_byline and faq_json columns to blog_posts, used by
-- the admin panel's "Author Byline" field and the FAQ question/answer
-- builder. Run this once against your live database (e.g. via phpMyAdmin or
-- the mysql CLI) if the table was created before these columns existed.
-- New installs using schema.sql already include these columns and do not
-- need this file.

ALTER TABLE blog_posts
    ADD COLUMN author_byline VARCHAR(255) NULL AFTER meta_description,
    ADD COLUMN faq_json LONGTEXT NULL AFTER content;
