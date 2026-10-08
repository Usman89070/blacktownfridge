<?php
/**
 * Branded 404 page. Included by single-post.php for an unknown blog slug
 * (after http_response_code(404) has already been set), and can also be
 * requested directly for any other missing page.
 */
if (!headers_sent() && http_response_code() !== 404) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Page Not Found | Fridge Repairs Blacktown</title>
<meta name="robots" content="noindex, follow">

<link rel="icon" type="image/webp" href="/images/logo.webp">
<link rel="apple-touch-icon" href="/images/logo.webp">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
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
    html { scroll-behavior: smooth; scroll-padding-top: 90px; }
    body { margin: 0; background-color: var(--bg-light); overflow-x: hidden; font-family: var(--font-main); color: var(--text-dark); -webkit-font-smoothing: antialiased; }
    a { text-decoration: none; transition: var(--transition); }

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

    .frb-container { width: 90%; max-width: 1140px; margin: 0 auto; }

    .frb-btn { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 16px 36px; border-radius: 50px; font-weight: 700; text-transform: uppercase; font-size: 0.9em; border: none; background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%); color: var(--white); cursor: pointer; transition: var(--transition); box-shadow: 0 8px 20px rgba(230, 57, 70, 0.2); }
    .frb-btn:hover { color: var(--white); transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 25px rgba(230, 57, 70, 0.3); }
    .frb-btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; }
    .frb-btn-outline:hover { background-color: var(--primary); color: var(--white); transform: translateY(-3px) scale(1.02); }

    /* MOBILE STICKY CTA */
    .frb-mobile-cta { display: none; position: fixed; bottom: 0; left: 0; width: 100%; z-index: 9998; }
    .frb-mobile-cta .frb-btn { width: 100%; border-radius: 0; padding: 20px; font-size: 1.05rem; box-shadow: 0 -5px 25px rgba(15, 61, 129, 0.2); }

    /* SERVICES DROPDOWN */
    .frb-has-dropdown { position: relative; }
    .frb-dropdown-toggle { display: none; position: absolute; top: 0; right: 0; width: 46px; height: 100%; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: var(--primary); font-size: 0.85em; padding: 0; }
    @media (min-width: 993px) {
        .frb-has-dropdown { display: inline-flex; align-items: center; }
        .frb-dropdown-toggle { display: inline-flex; position: static; width: auto; height: auto; min-width: 20px; min-height: 20px; margin-left: 4px; padding: 2px; }
    }
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

    /* 404 CONTENT */
    .frb-404-section { width: 90%; max-width: 640px; margin: 0 auto; padding: 180px 0 100px; text-align: center; }
    .frb-404-code { font-size: clamp(60px, 12vw, 110px); font-weight: 800; color: var(--primary); margin: 0; line-height: 1; }
    .frb-404-section h1 { font-size: clamp(24px, 4vw, 32px); color: var(--text-dark); margin: 10px 0 15px; font-weight: 700; }
    .frb-404-section p { color: var(--text-light); font-size: 1.05em; line-height: 1.7; margin-bottom: 35px; }
    .frb-404-actions { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; }

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
    .frb-footer-note { text-align: center; padding: 18px 20px; font-size: 0.8em; color: var(--text-light); background: var(--bg-light); border-top: 1px solid rgba(0,0,0,0.05); }

    @media (max-width:576px) { .frb-footer-grid { gap: 30px; } }

    @media (max-width: 992px) {
        .frb-header-actions { display: none; }
        .frb-mobile-toggle { display: block; }
        .frb-main-nav { position: absolute; top: 100%; left: 0; width: 100%; background-color: var(--white); display: none; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: calc(100vh - 90px); overflow-y: auto; border-top: 1px solid rgba(0,0,0,0.05); z-index: 10000; }
        .frb-main-nav.active { display: flex !important; }
        .frb-main-nav ul { flex-direction: column; gap: 0; }
        .frb-main-nav ul li a { display: block; padding: 16px 20px; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: center; }
        .frb-main-nav ul li a::after { display: none; }
        .frb-dropdown-toggle { display: flex; }
        .frb-main-nav .frb-has-dropdown > a { padding-right: 46px; }
        .frb-main-nav .frb-dropdown-menu { position: static; transform: none; opacity: 1; visibility: visible; pointer-events: auto; box-shadow: none; background: var(--primary-light); max-height: 0; overflow: hidden; padding: 0; border-radius: 0; transition: max-height 0.3s ease; }
        .frb-has-dropdown.frb-dropdown-open .frb-dropdown-menu { max-height: 280px; transform: none; }
        .frb-main-nav .frb-dropdown-menu li a { padding: 14px 20px 14px 40px; border-bottom: 1px solid rgba(15,61,129,0.08); font-size: 0.95rem; text-align: center; white-space: normal; }
        .frb-main-nav .frb-dropdown-menu li:last-child a { border-bottom: none; }
        .frb-mobile-cta { display: block; }
        body { padding-bottom: 65px; }
        .frb-404-section { padding: 160px 0 80px; }
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
                    <li><a href="/blog/">Blog</a></li>
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

    <!-- 404 CONTENT -->
    <section class="frb-404-section">
        <p class="frb-404-code">404</p>
        <h1>Page Not Found</h1>
        <p>Sorry, the page you're looking for doesn't exist or may have been moved. It might have been removed, renamed, or the link may be incorrect.</p>
        <div class="frb-404-actions">
            <a href="/" class="frb-btn"><i class="fa-solid fa-house"></i> Back to Home</a>
            <a href="/blog/" class="frb-btn frb-btn-outline"><i class="fa-solid fa-book-open"></i> Visit the Blog</a>
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
    <p class="frb-footer-note">This site is independent and is not affiliated with or endorsed by any of the brands listed.</p>

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
    });
</script>
</body>
</html>
