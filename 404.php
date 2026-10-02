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
        --text-dark: #1E293B;
        --text-light: #64748B;
        --bg-light: #F8FAFC;
        --white: #ffffff;
        --font-main: 'Montserrat', sans-serif;
        --radius: 16px;
        --shadow-3d: 0 15px 35px -5px rgba(15, 61, 129, 0.15), 0 5px 15px rgba(0,0,0,0.05);
        --transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }
    * { box-sizing: border-box; }
    body { margin: 0; background-color: var(--bg-light); font-family: var(--font-main); color: var(--text-dark); -webkit-font-smoothing: antialiased; }
    a { text-decoration: none; transition: var(--transition); }

    .frb-site-header { width: 100%; background-color: rgba(255, 255, 255, 0.95); box-shadow: 0 4px 20px rgba(15, 61, 129, 0.08); }
    .frb-header-container { width: 90%; max-width: 1140px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 12px 0; }
    .frb-logo img { max-height: 65px; width: auto; display: block; }

    .frb-404-section { width: 90%; max-width: 640px; margin: 0 auto; padding: 100px 0; text-align: center; }
    .frb-404-code { font-size: clamp(60px, 12vw, 110px); font-weight: 800; color: var(--primary); margin: 0; line-height: 1; }
    .frb-404-section h1 { font-size: clamp(24px, 4vw, 32px); color: var(--text-dark); margin: 10px 0 15px; }
    .frb-404-section p { color: var(--text-light); font-size: 1.05em; line-height: 1.7; margin-bottom: 35px; }
    .frb-404-actions { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; }
    .frb-btn { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 14px 32px; border-radius: 50px; font-weight: 700; text-transform: uppercase; font-size: 0.85em; border: none; background: #e02431; color: var(--white); cursor: pointer; box-shadow: 0 8px 25px rgba(224, 36, 49, 0.3); letter-spacing: 0.5px; }
    .frb-btn:hover { color: var(--white); transform: translateY(-2px); }
    .frb-btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; }
    .frb-btn-outline:hover { background-color: var(--primary); color: var(--white); }

    .frb-footer { background: linear-gradient(135deg, var(--primary-dark), #051530); color: rgba(255,255,255,0.75); padding: 30px 0; text-align: center; font-size: 0.9em; }
</style>
</head>
<body>

<header class="frb-site-header">
    <div class="frb-header-container">
        <div class="frb-logo">
            <a href="/"><img src="/images/logo.webp" alt="Fridge Repairs Blacktown Logo" width="200" height="65"></a>
        </div>
    </div>
</header>

<section class="frb-404-section">
    <p class="frb-404-code">404</p>
    <h1>Page Not Found</h1>
    <p>Sorry, the page you're looking for doesn't exist or may have been moved. It might have been removed, renamed, or the link may be incorrect.</p>
    <div class="frb-404-actions">
        <a href="/" class="frb-btn"><i class="fa-solid fa-house"></i> Back to Home</a>
        <a href="/blog/" class="frb-btn frb-btn-outline"><i class="fa-solid fa-book-open"></i> Visit the Blog</a>
    </div>
</section>

<footer class="frb-footer">
    <p>&copy; 2026 Fridge Repairs Blacktown. All Rights Reserved.</p>
</footer>

</body>
</html>
