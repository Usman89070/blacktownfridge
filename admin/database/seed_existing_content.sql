-- Seeds the admin panel with the gallery images and customer reviews that were
-- already hardcoded in index.html, so they show up in /admin without retyping.
--
-- Run this AFTER schema.sql:
--   mysql -u your_user -p your_database < admin/database/seed_existing_content.sql
--
-- The corresponding image files (show1.jpeg ... show5.jpeg, blacktown.webp) have
-- already been copied into admin/uploads/gallery/ alongside this file.
--
-- Note: the original review cards on the site only ever showed a 5-star rating
-- and quote text — no customer name was captured anywhere in the markup — so
-- these are seeded as "Verified Customer". Edit each one in
-- admin/testimonial_form.php to add the real customer's name if you have it.

INSERT INTO gallery_images (file_path, alt_text, orientation, sort_order) VALUES
    ('show1.jpeg', 'Fridge Repair Work 1', 'landscape', 1),
    ('show2.jpeg', 'Fridge Repair Work 2', 'landscape', 2),
    ('show3.jpeg', 'Fridge Repair Work 3', 'portrait', 3),
    ('show4.jpeg', 'Fridge Repair Work 4', 'portrait', 4),
    ('show5.jpeg', 'Fridge Repair Work 5', 'portrait', 5),
    ('blacktown.webp', 'Fridge Repair Work 6', 'portrait', 6);

INSERT INTO testimonials (customer_name, rating, review_text, is_published) VALUES
    ('Verified Customer', 5, 'My fridge suddenly stopped cooling overnight and I booked online first thing in the morning. The technician arrived the same day, explained the issue clearly, and had it running again much quicker than I expected. Friendly, professional, and no hidden surprises. Highly recommend if you need fridge repairs in Blacktown.', 1),
    ('Verified Customer', 5, 'Excellent service from start to finish. I appreciated the quick response and honest advice rather than being pressured into replacing my fridge. The repair was completed on the spot, and everything has been working perfectly since. Great experience and I''ll definitely use them again if needed.', 1),
    ('Verified Customer', 5, 'Our cafe fridge failed during a busy weekend, so we needed urgent help. They managed to fit us in the same day and got everything back up and running before we lost too much stock. Professional technicians who clearly know commercial refrigeration. Fantastic service.', 1),
    ('Verified Customer', 5, 'Very impressed with how easy the online booking process was. The technician arrived on time, diagnosed the problem quickly, and explained exactly what needed to be repaired. The pricing was fair, and the workmanship was excellent. It''s reassuring to know the repair is backed by a warranty.', 1),
    ('Verified Customer', 5, 'I called after hours when our freezer stopped working, and they responded much faster than I expected. The technician was courteous, knowledgeable, and fixed the issue without any fuss. It''s hard to find reliable trades these days, but these guys definitely delivered. Five stars all the way.', 1);
