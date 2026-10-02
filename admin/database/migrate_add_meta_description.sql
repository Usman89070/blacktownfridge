-- Migration: add the meta_description column to an existing blog_posts table.
-- Run this once against your live database (e.g. via phpMyAdmin or the mysql
-- CLI) if the table was created before this column existed. New installs
-- using schema.sql already include this column and do not need this file.

ALTER TABLE blog_posts
    ADD COLUMN meta_description VARCHAR(160) AFTER excerpt;
