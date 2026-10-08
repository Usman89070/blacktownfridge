<?php
/**
 * Server-rendered blog index. The post list is queried from the database
 * and output as real <a href="/blog/slug"> links in the HTML so search
 * engines can crawl every post without running JavaScript.
 */
require __DIR__ . '/admin/config.php';
require __DIR__ . '/admin/includes/functions.php';

$posts = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog & Tips | Fridge Repairs Blacktown</title>
<meta name="description" content="Read the latest news, maintenance tips, and troubleshooting guides for your residential and commercial refrigerators in Blacktown.">
<link rel="canonical" href="<?= e(SITE_URL) ?>/blog/">

<!-- Favicon (Logo in browser tab) -->
<link rel="icon" type="image/webp" href="/images/logo.webp">
<link rel="apple-touch-icon" href="/images/logo.webp">

<!-- Preconnect for faster font/icon loading -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

<link rel="preload" as="image" fetchpriority="high" href="/images/blog.webp">

<!-- Fonts -->
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #0F3D81;
        --primary-light: #EBF1F8;
        --primary-dark: #0A2959;
        --secondary: #E63946;
        --secondary-dark: #C1121F;
        --text-dark: #1E293B;
        --text-light: #64748B;
        --bg-light: #F8FAFC;
        --white: #ffffff;
        --font-main: 'Montserrat', sans-serif;
        --radius: 16px;
        --radius-sm: 8px;
        --shadow-sm: 0 4px 14px rgba(15, 61, 129, 0.05);
        --shadow-3d: 0 15px 35px -5px rgba(15, 61, 129, 0.15), 0 5px 15px rgba(0,0,0,0.05);
        --shadow-hover: 0 25px 50px -12px rgba(15, 61, 129, 0.25), 0 15px 15px -10px rgba(15, 61, 129, 0.15);
        --transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }

    * { box-sizing: border-box; }

    /* Exact offset for fixed header */
    html { scroll-behavior: smooth; scroll-padding-top: 90px; }

    body { margin: 0; background-color: var(--bg-light); overflow-x: hidden; -webkit-font-smoothing: antialiased; }

    .frb-reveal { opacity: 0; transform: perspective(1200px) translateY(50px) translateZ(-50px) rotateX(-8deg); transition: opacity 0.8s cubic-bezier(0.25, 1, 0.5, 1), transform 0.8s cubic-bezier(0.25, 1, 0.5, 1); will-change: opacity, transform; }
    .frb-reveal.active { opacity: 1; transform: perspective(1200px) translateY(0) translateZ(0) rotateX(0deg); }

    .frb-global-styles { font-family: var(--font-main); font-size: 16px; color: var(--text-dark); line-height: 1.7; }
    .frb-global-styles a { text-decoration: none; transition: var(--transition); }
    .frb-global-styles h1, .frb-global-styles h2, .frb-global-styles h3 { color: var(--primary); margin-top: 0; font-weight: 700; letter-spacing: -0.5px; }

    /* HEADER & BLUR SCROLL EFFECT */
    .frb-site-header {
        width: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 4px 20px rgba(15, 61, 129, 0.08);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
        transition: background-color 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease, -webkit-backdrop-filter 0.3s ease;
    }
    .frb-site-header.scrolled {
        background-color: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 8px 25px rgba(15, 61, 129, 0.12);
    }
    .frb-header-container { width: 90%; max-width: 1140px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 12px 0; }
    .frb-logo img { max-height: 65px; width: auto; display: block; transition: var(--transition); }
    .frb-logo img:hover { transform: scale(1.03) translateY(-2px); }
    .frb-main-nav { flex-grow: 1; display: flex; justify-content: center; }
    .frb-main-nav ul { list-style: none; display: flex; gap: 35px; margin: 0; padding: 0; }
    .frb-main-nav ul li a { color: var(--text-dark); font-weight: 600; font-size: 0.95rem; text-transform: capitalize; position: relative; padding-bottom: 6px; }
    .frb-main-nav ul li a::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 2px; background-color: var(--secondary); transition: var(--transition); border-radius: 2px; }

    .frb-main-nav ul li a.active { color: var(--primary); }
    .frb-main-nav ul li a.active::after { width: 100%; }

    @media (hover: hover) and (pointer: fine) {
        .frb-main-nav ul li a:hover { color: var(--primary); }
        .frb-main-nav ul li a:hover::after { width: 100%; }
    }

    .frb-header-actions { display: flex; align-items: center; gap: 20px; }
    .frb-mobile-toggle { display: none; background: none; border: none; font-size: 1.8rem; cursor: pointer; color: var(--primary); }

    .frb-container { width: 90%; max-width: 1140px; margin: 0 auto; padding: 80px 0; }

    .frb-btn { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 16px 36px; border-radius: 50px; font-weight: 700; text-transform: uppercase; font-size: 0.9em; border: none; background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%); color: var(--white); cursor: pointer; transition: var(--transition); box-shadow: 0 8px 20px rgba(230, 57, 70, 0.2); }
    .frb-btn:hover { color: var(--white); transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 25px rgba(230, 57, 70, 0.3); }
    .frb-btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; }
    .frb-btn-outline:hover { background-color: var(--primary); color: var(--white); transform: translateY(-3px) scale(1.02); }

    /* MOBILE STICKY CTA */
    .frb-mobile-cta {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 9998;
    }

    .frb-mobile-cta .frb-btn {
        width: 100%;
        border-radius: 0;
        padding: 20px;
        font-size: 1.05rem;
        box-shadow: 0 -5px 25px rgba(15, 61, 129, 0.2);
    }

    /* BANNER */
    .frb-page-banner { background: linear-gradient(135deg, rgba(15, 61, 129, 0.92), rgba(10, 41, 89, 0.9)), url('/images/blog.webp') center/cover no-repeat; padding: 160px 0 80px; text-align: center; color: var(--white); }
    .frb-page-banner h1 { font-size: clamp(32px, 5vw, 46px); color: var(--white); margin-bottom: 10px; border: none; }
    .frb-page-banner p { font-size: 1.15em; color: rgba(255,255,255,0.85); max-width: 600px; margin: 0 auto; }

    /* BLOG GRID */
    .frb-blog-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 20px; }
    .frb-blog-card { background: var(--white); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-3d); border: 1px solid rgba(255,255,255,0.8); border-top: 2px solid #ffffff; display: flex; flex-direction: column; transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease; transform-style: preserve-3d; backface-visibility: hidden; }
    .frb-blog-card:hover { transform: translateY(-8px) perspective(1000px) rotateX(2deg); box-shadow: var(--shadow-hover); }
    .frb-blog-card:hover .frb-blog-img { transform: scale(1.05); }
    .frb-blog-img-wrapper { overflow: hidden; width: 100%; aspect-ratio: 672 / 372; border-bottom: 3px solid var(--secondary); background-color: var(--primary-light); }
    .frb-blog-img-link { display: block; width: 100%; height: 100%; }
    .frb-blog-img { width: 100%; height: 100%; object-fit: contain; transition: transform 0.6s ease; }
    .frb-blog-content { padding: 25px; display: flex; flex-direction: column; flex-grow: 1; transform: translateZ(10px); }
    .frb-blog-title { font-size: 1.25em; margin-bottom: 12px; color: var(--primary); line-height: 1.4; }
    .frb-blog-title a { color: inherit; }
    .frb-blog-title a:hover { color: var(--secondary); }
    .frb-blog-excerpt { color: var(--text-light); font-size: 0.95em; margin-bottom: 20px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .frb-blog-link { color: var(--secondary); font-weight: 700; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9em; text-transform: uppercase; margin-top: auto;}
    .frb-blog-link:hover { color: var(--primary); }
    .frb-blog-empty { text-align: center; padding: 60px; color: var(--text-light); font-weight: 500; font-size: 1.2em; grid-column: 1 / -1; }

    /* ================= MODERN FOOTER ================= */
    .frb-footer { background: linear-gradient(135deg, var(--primary-dark), #051530); color: var(--white); padding: 80px 0 20px; font-family: var(--font-main); box-shadow: inset 0 10px 20px rgba(0,0,0,0.5); }
    .frb-footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px; width: 90%; max-width: 1140px; margin: 0 auto; }
    .frb-footer-logo-wrapper { background: var(--white); padding: 10px 20px; border-radius: var(--radius-sm); display: inline-block; box-shadow: var(--shadow-sm); margin-bottom: 15px; }
    .frb-footer-brand p { color: rgba(255, 255, 255, 0.75); font-size: 0.95em; line-height: 1.7; margin-top: 10px; }
    .frb-footer-contact { list-style: none; padding: 0; margin-top: 20px; }
    .frb-footer-contact li { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 15px; color: rgba(255, 255, 255, 0.9); overflow-wrap: anywhere; word-break: break-word; }
    .frb-footer-contact i { color: var(--secondary); font-size: 1.1em; flex-shrink: 0; margin-top: 4px; }
    .frb-footer-contact a { color: inherit; text-decoration: none; transition: var(--transition); }
    @media (hover: hover) { .frb-footer-contact a:hover { color: var(--white); text-shadow: 0 0 10px rgba(255,255,255,0.5); } }
    .frb-footer-col h4 { color: var(--white); font-size: 1.15em; margin-top: 0; margin-bottom: 25px; position: relative; padding-bottom: 12px; font-weight: 700; letter-spacing: 0.5px; }
    .frb-footer-col h4::after { content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; border-radius: 3px; background: var(--secondary); box-shadow: 0 2px 5px rgba(230,57,70,0.5); }
    .frb-footer-col ul { list-style: none; padding: 0; margin: 0; }
    .frb-footer-col ul li { margin-bottom: 12px; }
    .frb-footer-col ul li a { color: rgba(255,255,255,0.75); transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; font-size: 0.95em; }
    .frb-footer-col ul li a::before { content: '\f105'; font-family: 'Font Awesome 6 Free'; font-weight: 900; font-size: 0.8em; color: var(--secondary); opacity: 0; transform: translateX(-10px); transition: var(--transition); }
    @media (hover: hover) { .frb-footer-col ul li a:hover { color: var(--white); transform: translateX(5px); } .frb-footer-col ul li a:hover::before { opacity: 1; transform: translateX(0); } }
    .frb-copyright { text-align: center; margin-top: 60px; padding-top: 25px; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6); font-size: 0.9em; max-width: 1140px; margin-left: auto; margin-right: auto; width: 90%; }

    @media (max-width:576px) {
        .frb-footer-grid { gap: 30px; }
    }

@media (max-width: 992px) {
        .frb-header-actions { display: none; }
        .frb-mobile-toggle { display: block; }

        .frb-main-nav {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: var(--white);
            display: none;
            flex-direction: column;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-height: calc(100vh - 90px);
            overflow-y: auto;
            border-top: 1px solid rgba(0,0,0,0.05);
            z-index: 10000;
        }

        .frb-main-nav.active { display: flex !important; }

        .frb-main-nav ul { flex-direction: column; gap: 0; }

        .frb-main-nav ul li a { display: block; padding: 16px 20px; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: center; }
        .frb-main-nav ul li a::after { display: none; }

        .frb-mobile-cta { display: block; }
        body { padding-bottom: 65px; }
    }

    /* SERVICES DROPDOWN */
    .frb-has-dropdown { position: relative; }
    .frb-dropdown-toggle { display: none; position: absolute; top: 0; right: 0; width: 46px; height: 100%; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: var(--primary); font-size: 0.85em; padding: 0; }
    .frb-dropdown-toggle i { transition: transform 0.3s ease; }
    .frb-has-dropdown.frb-dropdown-open .frb-dropdown-toggle i { transform: rotate(180deg); }
    .frb-main-nav .frb-dropdown-menu { display: block; list-style: none; margin: 0; padding: 10px; position: absolute; top: 100%; left: 50%; transform: translateX(-50%) translateY(8px); background: var(--white); min-width: 250px; border-radius: var(--radius-sm); box-shadow: var(--shadow-hover); opacity: 0; visibility: hidden; pointer-events: none; transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s; z-index: 10001; }
    .frb-main-nav .frb-dropdown-menu li { margin: 0; width: auto; text-align: left; border-bottom: none; }
    .frb-main-nav .frb-dropdown-menu li a { display: block; padding: 10px 14px; border-radius: 6px; color: var(--text-dark); font-weight: 600; font-size: 0.92rem; white-space: nowrap; text-align: left; }
    .frb-main-nav .frb-dropdown-menu li a::after { display: none; }
    .frb-main-nav .frb-dropdown-menu li a:hover { background: var(--primary-light); color: var(--primary); }
    @media (hover: hover) and (pointer: fine) {
        .frb-has-dropdown:hover .frb-dropdown-menu { opacity: 1; visibility: visible; pointer-events: auto; transform: translateX(-50%) translateY(0); }
    }
    .frb-has-dropdown.frb-dropdown-open .frb-dropdown-menu { opacity: 1; visibility: visible; pointer-events: auto; transform: translateX(-50%) translateY(0); }
    @media (max-width: 992px) {
        .frb-dropdown-toggle { display: flex; }
        .frb-main-nav .frb-has-dropdown > a { padding-right: 46px; }
        .frb-main-nav .frb-dropdown-menu { position: static; transform: none; opacity: 1; visibility: visible; pointer-events: auto; box-shadow: none; background: var(--primary-light); max-height: 0; overflow: hidden; padding: 0; border-radius: 0; transition: max-height 0.3s ease; }
        .frb-has-dropdown.frb-dropdown-open .frb-dropdown-menu { max-height: 280px; transform: none; }
        .frb-main-nav .frb-dropdown-menu li a { padding: 14px 20px 14px 40px; border-bottom: 1px solid rgba(15,61,129,0.08); font-size: 0.95rem; text-align: center; white-space: normal; }
        .frb-main-nav .frb-dropdown-menu li:last-child a { border-bottom: none; }
    }
</style>
</head>
<body>

<div class="frb-global-styles">

    <!-- HEADER -->
    <header class="frb-site-header">
        <div class="frb-header-container">
            <div class="frb-logo">
                <a href="/"><img src="/images/logo.webp" alt="Fridge Repairs Blacktown Logo" width="200" height="65"></a>
            </div>
            <nav class="frb-main-nav" id="frb-mobile-nav">
                <ul>
                    <li><a href="/#hero">Home</a></li>
                    <li><a href="/about-us/">About Us</a></li>
                    <li class="frb-has-dropdown">
                        <a href="/#services">Services</a>
                        <button type="button" class="frb-dropdown-toggle" aria-expanded="false" aria-label="Toggle Services menu"><i class="fa-solid fa-chevron-down"></i></button>
                        <ul class="frb-dropdown-menu">
                            <li><a href="/commercial-fridge-repair-blacktown/">Commercial Fridge Repair</a></li>
                            <li><a href="/freezer-repair-blacktown/">Freezer Repair</a></li>
                            <li><a href="/emergency-fridge-repair/">Emergency Fridge Repair</a></li>
                            <li><a href="/#services">View All Services</a></li>
                        </ul>
                    </li>
                    <li><a href="/#brands">Brands</a></li>
                    <li><a href="/blog/" class="active">Blog</a></li>
                    <li><a href="/#image-grid">Gallery</a></li>
                    <li><a href="/#faq-section">FAQ</a></li>
                    <li><a href="/#contact">Contact</a></li>
                </ul>
            </nav>
            <div class="frb-header-actions">
                <a href="/#contact-form" class="frb-btn frb-header-btn"><i class="fa-solid fa-calendar-check"></i> Book Now</a>
            </div>
            <button class="frb-mobile-toggle" id="frb-menu-btn"><i class="fa-solid fa-bars"></i></button>
        </div>
    </header>

    <!-- PAGE BANNER -->
    <section class="frb-page-banner">
        <div class="frb-container" style="padding: 0;">
            <div class="frb-reveal active">
                <h1>Latest News & Tips</h1>
                <p>Read our latest advice on refrigeration maintenance, efficiency, saving money, and quick troubleshooting tips.</p>
            </div>
        </div>
    </section>

    <!-- BLOG GRID -->
    <section class="frb-container" style="padding-top: 40px; min-height: 50vh;">
        <div class="frb-blog-grid" id="frb-blog-container">
            <?php if (empty($posts)): ?>
                <div class="frb-blog-empty">No blog posts published yet. Check back soon!</div>
            <?php else: ?>
                <?php foreach ($posts as $index => $post): ?>
                    <?php
                    $link = '/blog/' . $post['slug'];
                    $imgUrl = $post['featured_image'] ? '/admin/' . UPLOAD_URL_BLOG . '/' . $post['featured_image'] : 'https://placehold.co/672x372/EBF1F8/0F3D81?text=Fridge+Repairs+Blacktown';
                    $delay = ($index % 3) * 0.1;
                    ?>
                    <div class="frb-blog-card frb-reveal active" style="transition-delay: <?= $delay ?>s">
                        <div class="frb-blog-img-wrapper">
                            <a href="<?= e($link) ?>" class="frb-blog-img-link">
                                <img src="<?= e($imgUrl) ?>" alt="<?= e($post['title']) ?>" class="frb-blog-img" loading="lazy">
                            </a>
                        </div>
                        <div class="frb-blog-content">
                            <h3 class="frb-blog-title"><a href="<?= e($link) ?>"><?= e($post['title']) ?></a></h3>
                            <div class="frb-blog-excerpt"><?= e($post['excerpt']) ?></div>
                            <a href="<?= e($link) ?>" class="frb-blog-link">Read Article <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="frb-footer">
        <div class="frb-footer-grid">
            <div class="frb-footer-col frb-footer-brand">
                <div class="frb-footer-logo-wrapper">
                    <img src="/images/logo.webp" alt="Fridge Repairs Blacktown Logo" width="200" height="65" loading="lazy" decoding="async" style="max-height: 50px; width: auto; display: block;">
                </div>
                <p>Providing reliable residential and commercial fridge repairs across Blacktown and surrounding suburbs.</p>
                <ul class="frb-footer-contact">
                    <li><i class="fa-solid fa-envelope"></i> <a href="mailto:info@fridgerepairblacktown.com.au">info@fridgerepairblacktown.com.au</a></li>
                </ul>
            </div>
            <div class="frb-footer-col">
                <h4>Our Services</h4>
                <ul>
                    <li><a href="/#services">Home Fridge Repairs</a></li>
                    <li><a href="/commercial-fridge-repair-blacktown/">Commercial Fridge Repairs</a></li>
                    <li><a href="/freezer-repair-blacktown/">Freezer Repairs</a></li>
                    <li><a href="/#services">Coolroom Repairs</a></li>
                    <li><a href="/#services">Fridge Regassing</a></li>
                    <li><a href="/#services">Display Fridge Repairs</a></li>
                    <li><a href="/#services">Salad Bar Fridge Repairs</a></li>
                    <li><a href="/#services">Commercial Exhaust System Repairs</a></li>
                    <li><a href="/emergency-fridge-repair/">Emergency Fridge Repairs</a></li>
                    <li><a href="/emergency-fridge-repair/">Urgent Fridge Repairs</a></li>
                </ul>
            </div>
            <div class="frb-footer-col">
                <h4>Brands We Repair</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px 40px;">
                    <ul style="flex: 1; min-width: 100px;">
                        <li><a href="/#brands">LG</a></li>
                        <li><a href="/#brands">Samsung</a></li>
                        <li><a href="/#brands">Westinghouse</a></li>
                        <li><a href="/#brands">Fisher & Paykel</a></li>
                        <li><a href="/#brands">Electrolux</a></li>
                        <li><a href="/#brands">Bosch</a></li>
                    </ul>
                    <ul style="flex: 1; min-width: 100px;">
                        <li><a href="/#brands">Skope</a></li>
                        <li><a href="/#brands">Hoshizaki</a></li>
                        <li><a href="/#brands">Williams</a></li>
                        <li><a href="/#brands">Polar</a></li>
                        <li><a href="/#brands">Skipio</a></li>
                        <li><a href="/#brands">FED</a></li>
                        <li><a href="/#brands">Liebherr</a></li>
                    </ul>
                </div>
            </div>
            <div class="frb-footer-col">
                <h4>Areas We Serve</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px 40px;">
                    <ul style="flex: 1; min-width: 100px;">
                        <li><a href="/#areas">Blacktown</a></li>
                        <li><a href="/#areas">Seven Hills</a></li>
                        <li><a href="/#areas">Doonside</a></li>
                        <li><a href="/#areas">Marayong</a></li>
                        <li><a href="/#areas">Lalor Park</a></li>
                        <li><a href="/#areas">Prospect</a></li>
                    </ul>
                    <ul style="flex: 1; min-width: 100px;">
                        <li><a href="/#areas">Quakers Hill</a></li>
                        <li><a href="/#areas">Rooty Hill</a></li>
                        <li><a href="/#areas">Mount Druitt</a></li>
                        <li><a href="/#areas">Glenwood</a></li>
                        <li><a href="/#areas">Stanhope Gardens</a></li>
                        <li><a href="/#areas">Kellyville Ridge</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="frb-copyright">
            <p style="margin-bottom: 5px;">Servicing Blacktown and surrounding Western Sydney suburbs with same-day residential and commercial fridge, freezer, coolroom, and refrigeration repairs.</p>
            <p>&copy; 2026 Fridge Repairs Blacktown. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- MOBILE STICKY BUTTON -->
    <div class="frb-mobile-cta">
        <a href="/#contact-form" class="frb-btn">
            <i class="fa-solid fa-calendar-check"></i> Book Now
        </a>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Header Scroll Effect
        const siteHeader = document.querySelector('.frb-site-header');
        if (siteHeader) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 30) {
                    siteHeader.classList.add('scrolled');
                } else {
                    siteHeader.classList.remove('scrolled');
                }
            });
        }

        // Mobile Menu Toggle
        const menuBtn = document.getElementById('frb-menu-btn');
        const mobileNav = document.getElementById('frb-mobile-nav');
        if(menuBtn && mobileNav) {
            menuBtn.addEventListener('click', function() {
                mobileNav.classList.toggle('active');
                const icon = menuBtn.querySelector('i');
                if(mobileNav.classList.contains('active')) {
                    icon.classList.remove('fa-bars'); icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark'); icon.classList.add('fa-bars');
                }
            });
        }

        // Services Dropdown Toggle (mobile tap-to-expand; desktop also works via hover)
        document.querySelectorAll('.frb-dropdown-toggle').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parentLi = btn.closest('.frb-has-dropdown');
                const isOpen = parentLi.classList.toggle('frb-dropdown-open');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        // Close menu automatically with delay for smooth navigation
        const navLinks = document.querySelectorAll('.frb-main-nav ul li a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if(mobileNav && mobileNav.classList.contains('active')) {
                    setTimeout(() => {
                        mobileNav.classList.remove('active');
                        if (menuBtn) {
                            const icon = menuBtn.querySelector('i');
                            if (icon) {
                                icon.classList.remove('fa-xmark');
                                icon.classList.add('fa-bars');
                            }
                        }
                    }, 150);
                }
            });
        });

        // Exact Anchor Scrolling (Same-page click handler)
        document.querySelectorAll('a[href*="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const isSamePage = (this.pathname === window.location.pathname) ||
                                   (this.pathname === '/' && window.location.pathname === '/index.html');

                if (isSamePage) {
                    const targetId = this.hash;
                    if (targetId && targetId !== '#') {
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            e.preventDefault();
                            if(mobileNav && mobileNav.classList.contains('active')) {
                                mobileNav.classList.remove('active');
                            }
                            const headerOffset = 90;
                            const elementPosition = targetElement.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.scrollY - headerOffset;
                            window.scrollTo({ top: offsetPosition, behavior: "smooth" });
                            history.pushState(null, null, targetId);
                        }
                    }
                }
            });
        });
    });

    // Cross-Page Anchor Links Multi-Retry Fix on Load
    window.addEventListener('load', function() {
        if (window.location.hash) {
            const hash = window.location.hash;

            const exactScrollToHash = (retries = 0) => {
                const targetElement = document.querySelector(hash);
                if (targetElement) {
                    const headerOffset = 90;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.scrollY - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: retries === 0 ? "auto" : "smooth"
                    });

                    if (retries < 3) {
                        setTimeout(() => exactScrollToHash(retries + 1), 200 * (retries + 1));
                    }
                }
            };

            setTimeout(() => exactScrollToHash(0), 150);
        }
    });
</script>
</body>
</html>
