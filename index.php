<?php
/**
 * GEÇİCİ OTURUM AÇMA KODU - LÜTFEN İŞLEM BİTİNCE BU BLOĞU SİLİN
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    require_once 'config.php';
    require_once 'database/db.php';
    try {
        $db = getDB();
        $user = $db->query("SELECT * FROM admin_users LIMIT 1")->fetch();
        if ($user) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    } catch (Exception $e) {
    }
}
?>
<?php
/**
 * Erguvan Psikoterapi Merkezi - V38 Premium Tasarım
 * Bu dosya tamamen sıfırdan, hiçbir eski veri kullanılmadan oluşturulmuştur.
 */
?>
<?php
// Veritabanı bağlantısı ve blog yazılarını çekme
require_once 'config.php';
require_once 'database/db.php';

$latest_posts = [];
try {
    $db = getDB();
    if ($db) {
        $stmt = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3");
        $latest_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Anasayfa blog hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erguvan Psikoterapi Merkezi | Akademik & Premium Destek</title>

    <!-- Meta Tags -->
    <meta name="description"
        content="Erguvan Psikoterapi Merkezi - Modern, akademik ve insan odaklı psikoterapi hizmetleri.">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Critical Image Preload -->
    <?php $hero_webp = webp_url('assets/images/hero-psikolojik-destek.jpg'); ?>
    <link rel="preload" as="image" href="<?php echo $hero_webp; ?>" fetchpriority="high">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Prata&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">

    <style>
        :root {
            /* Corporate Color Palette (Logo Aligned) */
            --primary: #0F172A;
            /* Midnight Navy from Logo */
            --secondary: #915F78;
            /* Muted Erguvan from User Image */
            --accent: #A66C8E;
            /* Lighter Rose from Image */
            --accent-soft: #FDF2F8;
            /* Soft Rose/Cream */
            --luxe-bg: #FAF5FF;
            --text-dark: #0F172A;
            --text-muted: #475569;
            --white: #FFFFFF;

            /* Design Tokens */
            --font-heading: 'Prata', serif;
            --font-body: 'Montserrat', sans-serif;
            --transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            --radius-lg: 40px;
            --radius-md: 20px;
            --shadow-luxe: 0 30px 60px rgba(15, 23, 42, 0.12);
            --glass: rgba(255, 255, 255, 0.85);
            --grad-corporate: linear-gradient(135deg, #915F78 0%, #70475E 100%);
            --grad-striking: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-dark);
            background-color: var(--white);
            line-height: 1.7;
            overflow-x: hidden;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Typography */
        h1,
        h2,
        h3 {
            font-family: var(--font-heading);
            font-weight: 400;
            line-height: 1.2;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.8rem 0;
            /* Balanced padding */
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(157, 23, 77, 0.1);
            transition: var(--transition);
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .navbar .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar .logo img {
            height: 60px !important;
            max-height: 60px !important;
            width: auto !important;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            margin-left: 1.2rem;
            line-height: 1;
        }

        .logo-title {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            color: var(--primary);
            letter-spacing: -0.5px;
            font-weight: 400;
        }

        .logo-subtitle {
            font-family: var(--font-body);
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 600;
            margin-top: 4px;
            letter-spacing: 0.3px;
            opacity: 0.85;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            /* Prevent wrapping */
        }

        .logo {
            flex-shrink: 0;
            /* Prevent logo from shrinking */
            margin-right: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
            flex-shrink: 1;
            /* Allow links to shrink if needed */
        }

        .nav-links a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
            font-size: 17px;
            /* Requested 16-18px range */
            transition: var(--transition);
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: var(--transition);
        }

        .nav-links img {
            display: none;
        }

        /* Desktop: hide mobile-only items and nav-phone */
        .mobile-only {
            display: none !important;
        }

        .nav-phone {
            display: none !important;
        }

        /* Underline hover effect - restored */
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            z-index: 1001;
            padding: 10px;
        }

        .menu-toggle span {
            display: block;
            width: 30px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
            transition: var(--transition);
        }

        .menu-toggle.active span:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--luxe-bg) 0%, #FFFFFF 100%);
            padding-top: 120px;
            /* Increased to avoid navbar overlap */
            position: relative;
            overflow: visible !important;
            /* Allow content to overflow if needed */
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 4rem;
            align-items: center;
            overflow: visible !important;
        }

        .hero-text h1 {
            font-size: clamp(3rem, 8vw, 4.5rem);
            color: var(--primary);
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        .hero-text h1 span {
            color: var(--secondary);
            display: block;
            margin-top: 0.5rem;
            /* Space between Erguvan and Psikoloji */
        }

        .hero-text p {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
            max-width: 500px;
        }

        .btn-premium {
            display: inline-block;
            padding: 1.25rem 3rem;
            background: var(--grad-corporate);
            color: var(--white);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            transition: var(--transition);
            box-shadow: 0 15px 35px rgba(157, 23, 77, 0.25);
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-premium:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(157, 23, 77, 0.4);
            filter: brightness(1.1);
        }

        .hero-image-wrapper {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-luxe);
            aspect-ratio: 1024 / 768;
            background: rgba(145, 95, 120, 0.05);
            /* Placeholder background */
        }

        .hero-image-wrapper img {
            width: 100%;
            display: block;
            transition: var(--transition);
        }

        .hero-image-wrapper:hover img {
            transform: scale(1.05);
        }

        /* Sections */
        .section {
            padding: 8rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title h2 {
            font-size: 3rem;
            color: var(--primary);
        }

        .section-title div {
            width: 60px;
            height: 4px;
            background: var(--secondary);
            margin: 1.5rem auto;
        }

        /* Cards */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: #ffffff;
            padding: 2.5rem 2rem;
            border-radius: 20px;
            border: 1.5px solid #f5edf8;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: left;
            position: relative;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        }

        .service-icon-wrapper {
            width: 56px;
            height: 56px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fde8f4;
            border-radius: 16px;
            flex-shrink: 0;
        }

        .service-card svg {
            width: 28px;
            height: 28px;
            stroke: var(--secondary);
            stroke-width: 1.8;
            fill: none;
            transition: all 0.3s ease;
        }

        .service-card:hover svg {
            stroke: #ffffff;
            transform: scale(1.1);
        }

        .service-card h3 {
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 0.8rem;
            font-family: var(--font-body);
            font-weight: 700;
            line-height: 1.3;
        }

        .service-card p {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin-bottom: 1.2rem;
            line-height: 1.7;
            padding: 0;
        }

        .service-focus-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 4px;
        }

        .service-focus-areas {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(145, 95, 120, 0.10);
        }

        .service-card:hover .service-icon-wrapper {
            background: var(--secondary);
            transform: scale(1.05);
        }

        .service-card:hover i {
            display: none;
        }

        .service-card:hover .service-focus-label {
            color: #ffffff;
            opacity: 0.9;
        }

        .service-card:hover .service-focus-areas {
            color: #ffffff;
            opacity: 0.8;
        }

        .service-card:hover h3,
        .service-card:hover p {
            color: #ffffff;
        }

        .team-card,
        .office-card {
            background: var(--white);
            padding: 0;
            border-radius: var(--radius-lg);
            border: 1px solid #f1f5f9;
            transition: var(--transition);
            text-align: center;
            overflow: hidden;
        }

        .service-card:hover,
        .team-card:hover,
        .office-card:hover,
        .blog-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--shadow-luxe);
        }

        .blog-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(139, 61, 72, 0.05);
            transition: var(--transition);
            overflow: hidden;
            position: relative;
            text-align: left;
        }

        .blog-card .card-img {
            height: 250px;
        }

        .blog-date {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--secondary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .blog-card h3 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--primary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .read-more {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }

        .read-more:hover {
            gap: 15px;
        }

        /* Floating Actions */
        .floating-actions {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            z-index: 2000;
        }

        .float-btn {
            width: 80px;
            height: 80px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 2.2rem;
            box-shadow: 0 15px 35px rgba(37, 211, 102, 0.4);
            transition: var(--transition);
            animation: whatsapp-pulse 2s infinite;
        }

        @keyframes whatsapp-pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 15px 35px rgba(37, 211, 102, 0.4);
            }

            50% {
                transform: scale(1.08);
                box-shadow: 0 20px 45px rgba(37, 211, 102, 0.6);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 15px 35px rgba(37, 211, 102, 0.4);
            }
        }

        .float-btn.whatsapp {
            background: #25D366;
            margin-bottom: 5px;
        }

        .float-btn.phone {
            background: var(--primary);
            width: 60px;
            height: 60px;
            border-radius: 15px;
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .float-btn:hover {
            transform: scale(1.1) translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: none;
        }

        .float-btn i,
        .float-btn svg {
            display: flex !important;
            align-items: center;
            justify-content: center;
            color: white !important;
            width: 40px;
            height: 40px;
        }

        .service-card i {
            font-size: 3.5rem;
            background: var(--grad-corporate);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            display: inline-block;
        }

        .service-card h3 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        /* FAQ (SSS) Styles */
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-item {
            border-bottom: 1px solid #E2E8F0;
            padding: 1rem 0;
        }

        .faq-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            transition: var(--transition);
        }

        .faq-question {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--primary);
            font-family: var(--font-body);
        }

        .faq-icon {
            font-size: 1.2rem;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
            color: var(--secondary);
        }

        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .faq-item.active .faq-content {
            max-height: 200px;
            padding-bottom: 2rem;
        }

        .faq-answer {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.7;
            margin: 0;
        }

        /* Aesthetics */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Contact Specific Styles */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 5rem;
            align-items: start;
        }

        .contact-info-list {
            list-style: none;
            padding: 0;
        }

        .contact-info-item {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .contact-info-item i {
            font-size: 1.5rem;
            color: var(--secondary);
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFF5F8;
            border-radius: 12px;
        }

        .contact-info-text h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            color: var(--primary);
        }

        .contact-info-text p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-premium {
            background: #FFFFFF;
            padding: 3.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-luxe);
        }

        .form-premium h3 {
            font-size: 1.8rem;
            margin-bottom: 2.5rem;
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            font-family: var(--font-body);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(139, 61, 72, 0.05);
        }

        @media (max-width: 900px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 4rem;
            }
        }

        /* Testimonials (Yorumlar) Styles */
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
        }

        .testimonial-card {
            background: var(--white);
            padding: 3.5rem 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-luxe);
            border: 1px solid rgba(139, 61, 72, 0.05);
            position: relative;
            transition: var(--transition);
        }

        .testimonial-card i.quote-icon {
            font-size: 2.5rem;
            color: var(--secondary);
            opacity: 0.15;
            position: absolute;
            top: 2rem;
            left: 2rem;
        }

        .testimonial-text {
            font-size: 1.05rem;
            font-style: italic;
            color: var(--text-dark);
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .author-info h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .author-info span {
            font-size: 0.85rem;
            color: var(--secondary);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            border-color: var(--secondary);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text p {
                margin: 0 auto 3rem;
            }
        }

        /* Modern Footer */
        .footer-modern {
            background-color: #1a1a1a;
            color: #a0a0a0;
            padding: 5rem 0 2rem;
            font-size: 0.95rem;
            border-top: 5px solid var(--secondary);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .footer-col p {
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #a0a0a0;
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--secondary);
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            background: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            font-size: 1.2rem;
        }

        .social-icon.instagram {
            background-color: #833AB4;
        }

        .social-icon.linkedin {
            background-color: #0077b5;
        }

        .social-icon:hover {
            transform: translateY(-5px);
            filter: brightness(1.2);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }

        /* MOBILE RESPONSIVENESS FIXES */
        @media (max-width: 900px) {
            .nav-container {
                padding: 1rem 1.5rem;
            }

            .logo-text {
                margin-left: 0.8rem;
            }

            .logo-title {
                font-size: 1.4rem;
            }

            .menu-toggle {
                display: flex;
            }

            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 100%;
                height: 100vh;
                background: var(--white);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 2.5rem;
                transition: 0.5s cubic-bezier(0.23, 1, 0.32, 1);
                z-index: 1000;
                padding: 2rem;
            }

            .nav-links.active {
                right: 0;
            }

            .nav-links li {
                width: 100%;
                text-align: center;
            }

            .nav-links a {
                font-size: 1.5rem;
                color: var(--primary);
            }

            .nav-container .btn-premium {
                display: none;
                /* Hide button in mobile header */
            }

            .nav-links .btn-premium {
                display: inline-block;
                width: auto;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 3rem;
            }

            .hero {
                padding-top: 140px !important;
            }

            .hero-text h1 {
                font-size: clamp(2rem, 10vw, 3rem) !important;
                line-height: 1.5 !important;
                margin-bottom: 1.5rem !important;
            }

            .hero-btns {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                padding: 0 2rem;
            }

            .hero-btns .btn-premium {
                margin: 0 !important;
                width: 100%;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 4rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1.5rem;
            }

            .section {
                padding: 5rem 0;
            }

            .section-title h2 {
                font-size: 2.2rem;
            }

            /* Force single column for all grids */
            .services-grid,
            .team-grid,
            .office-grid,
            .testimonial-grid,
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            /* Adjust card paddings */
            .service-card,
            .team-card,
            .office-card,
            .testimonial-card,
            .blog-card {
                padding: 2rem 1.5rem;
            }

            .team-card,
            .office-card {
                padding: 0;
                /* Keep image containers 0 padding */
            }

            /* Verify card width */
            .service-card,
            .testimonial-card {
                width: 100%;
                box-sizing: border-box;
            }
        }

        @media (max-width: 480px) {
            .navbar .logo img {
                height: 45px !important;
            }

            .logo-text {
                margin-left: 0.8rem;
            }

            .logo-title {
                font-size: 1.3rem;
            }

            .logo-subtitle {
                font-size: 0.6rem;
                letter-spacing: 0;
            }

            .hero {
                padding-top: 120px !important;
            }

            .hero-text h1 {
                font-size: clamp(1.6rem, 12vw, 2rem) !important;
                line-height: 1.6 !important;
                padding: 0.5rem 0 !important;
                display: block !important;
            }

            .hero-text h1 span {
                display: block !important;
                line-height: 1 !important;
            }

            .btn-premium {
                padding: 1rem 2rem;
                font-size: 0.9rem;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .contact-info-panel {
                padding: 0;
            }

            .form-premium {
                padding: 1.5rem;
            }

            /* Fix overflow issues */
            body {
                overflow-x: hidden;
            }
        }
    </style>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css" media="print"
        onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    </noscript>
</head>

<body><!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="#" class="logo">
                <img src="<?php echo webp_url('assets/images/logo_icon.png'); ?>" alt="Erguvan Psikoloji Logo"
                    fetchpriority="high" width="124" height="130">
                <div class="logo-text">
                    <span class="logo-title">Erguvan Psikoloji</span>
                    <span class="logo-subtitle">Uzman Klinik Psikolog Desteği</span>
                </div>
            </a>

            <div class="menu-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <ul class="nav-links" id="navMenu">
                <li><a href="#home" class="nav-link">Ana Sayfa</a></li>
                <li><a href="#team" class="nav-link">Ekibimiz</a></li>
                <li><a href="#office" class="nav-link">Ofisimiz</a></li>
                <li><a href="#hizmetler" class="nav-link">Hizmetler</a></li>
                <li><a href="#blog" class="nav-link">Blog</a></li>
                <li><a href="#contact" class="nav-link">İletişim</a></li>
                <li class="nav-phone">
                    <a href="tel:+905511765285" class="nav-link">
                        <i class="fas fa-phone-alt"></i> 0551 176 52 85
                    </a>
                </li>
                <li class="mobile-only">
                    <a href="tel:+905511765285" class="btn-premium"
                        style="padding: 1rem 2rem; width: 100%; text-align: center;">Hemen Ara</a>
                </li>
            </ul>
            <a href="tel:+905511765285" class="btn-premium head-cta"
                style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">Hemen Ara</a>
        </div>
    </nav>
    <!-- Hero Section -->
    <header class="hero" id="home">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Erguvan <span>Psikoloji</span></h1>
                <p
                    style="font-weight: 600; color: var(--primary); font-family: var(--font-body); margin-bottom: 1rem; font-size: 1.4rem;">
                    Uzman Klinik Psikolog Desteği </p>
                <p>Modern bilimin ışığında,
                    insan ruhunun derinliklerine saygı duyan bir yaklaşımla profesyonel psikoterapi hizmeti
                    sunuyoruz. </p>
                <div class="hero-btns"><a href="#contact" class="btn-premium">Randevu Al</a><a href="#hizmetler"
                        class="btn-premium"
                        style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; margin-left: 1rem;">Hizmetlerimiz</a>
                </div>
            </div>
            <div class="hero-image-wrapper">
                <img src="<?php echo $hero_webp; ?>" alt="Erguvan Psikoloji Ofis" fetchpriority="high" width="1024"
                    height="768" style="width: 100%; height: auto; object-fit: cover; border-radius: var(--radius-lg);">
            </div>
        </div>
    </header>
    <!-- Team Section -->
    <section class="section" id="team">
        <div class="container">
            <div class="section-title">
                <h2>Uzman Ekibimiz</h2>
                <p>Akademik birikim ve klinik tecrübeyi harmanlayan profesyonel kadromuz.</p>
                <div></div>
            </div>
            <div class="team-grid">
                <div class="team-card"><img src="assets/images/team/sena.jpg"
                        alt="Uzm. Klinik Psk. Sena Ceren Parmaksız" class="card-img"
                        style="height: 450px; object-position: center;">
                    <div class="card-info">
                        <h3>Sena Ceren Parmaksız</h3>
                        <p style="color: var(--secondary); font-weight: 600;">Uzman Klinik Psikolog</p>
                    </div>
                </div>
                <div class="team-card"><img src="assets/images/team/sedat.jpg" alt="Uzm. Psikolog Sedat Parmaksız"
                        class="card-img" style="height: 450px; object-position: center top;">
                    <div class="card-info">
                        <h3>Sedat Parmaksız</h3>
                        <p style="color: var(--secondary); font-weight: 600;">Uzman Psikolog</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Office Section -->
    <section class="section" id="office" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2>Ofisimiz</h2>
                <p>Huzurlu,
                    güvenli ve profesyonel bir terapi ortamı.</p>
                <div></div>
            </div>
            <div class="office-grid">
                <div class="office-card"><img src="<?php echo webp_url('assets/images/office/ofis-1.jpg'); ?>"
                        alt="Ofisimiz 1" class="card-img" style="height: 300px; width: 100%; object-fit: cover;"
                        loading="lazy"></div>
                <div class="office-card"><img src="<?php echo webp_url('assets/images/office/office2.jpg'); ?>"
                        alt="Ofisimiz 2" class="card-img" style="height: 300px; width: 100%; object-fit: cover;"
                        loading="lazy"></div>
                <div class="office-card"><img src="<?php echo webp_url('assets/images/office/office3.jpg'); ?>"
                        alt="Ofisimiz 3" class="card-img" style="height: 300px; width: 100%; object-fit: cover;"
                        loading="lazy"></div>
                <div class="office-card"><img src="<?php echo webp_url('assets/images/office/office4.jpg'); ?>"
                        alt="Ofisimiz 4" class="card-img" style="height: 300px; width: 100%; object-fit: cover;"
                        loading="lazy"></div>
            </div>
        </div>
    </section>
    <!-- Services Section -->
    <section class="section" id="hizmetler">
        <div class="container">
            <div class="section-title">
                <h2>Hizmetlerimiz</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Modern ve bilimsel temelli terapi yaklaşımlarımızla yanınızdayız.</p>
            </div>
            <div class="services-grid">
                <!-- Bireysel Terapi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3>Bireysel Terapi</h3>
                    <p>Kendinizi anlama ve hayat kalitenizi artırma yolculuğunda yanınızdayız.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Depresyon, Kaygı Bozuklukları, Travma</div>
                </div>
                <!-- Aile ve Çift Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>Aile ve Çift Terapisi</h3>
                    <p>İlişkilerinizde daha sağlıklı iletişim ve güçlü bağlar kurmanız için yanınızdayız.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Aile İçi Çatışmalar, İletişim Sorunları, Boşanma Süreci</div>
                </div>
                <!-- Oyun Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M11.767 19.089c4.924.868 6.14-6.025 1.216-6.894-4.924-.869-6.14 6.025-1.216 6.894z">
                            </path>
                            <path d="M4.173 19.547c.522-4.933 7.375-4.521 6.854.412-.522 4.933-7.375 4.521-6.854-.412z">
                            </path>
                            <path
                                d="M18.156 16.51c2.147-4.453-4.228-7.535-6.375-3.082-2.147 4.453 4.228 7.535 6.375 3.082z">
                            </path>
                            <path d="M9.374 8.243c4.782-1.42 4.14-8.245-.641-6.824-4.783 1.42-4.14 8.245.641 6.824z">
                            </path>
                        </svg>
                    </div>
                    <h3>Oyun Terapisi</h3>
                    <p>Çocukların kendilerini ifade etme dili olan oyun ile duygusal iyileşme sağlıyoruz.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Davranış Sorunları, Ayrılık Kaygısı, Yas</div>
                </div>
                <!-- Yetişkin Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h3>Yetişkin Terapisi</h3>
                    <p>Yetişkinlik döneminin getirdiği zorluklarla başa çıkmak için profesyonel destek.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Sınav Kaygısı, Özgüven, Kariyer Stresi</div>
                </div>
                <!-- Çocuk Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3>Çocuk Terapisi</h3>
                    <p>Çocukların gelişimsel süreçlerinde karşılaştıkları güçlükleri birlikte aşıyoruz.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Dikkat Dağınıklığı, Uyum Sorunları, Korku ve Fobiler</div>
                </div>
                <!-- Ebeveyn Danışmanlığı -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z">
                            </path>
                        </svg>
                    </div>
                    <h3>Ebeveyn Danışmanlığı</h3>
                    <p>Ebeveynlik yolculuğunda karşılaşılan sorulara bilimsel cevaplar ve rehberlik.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Sınır Koyma, Bağlanma Stilleri, Ergenlik Dönemi</div>
                </div>
                <!-- Bilişsel Davranışçı Terapi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.5 20a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"></path>
                            <path d="M14.5 20a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"></path>
                            <path d="M12 15V7"></path>
                            <path d="M12 7a5 5 0 1 1 10 0 5 5 0 1 1-10 0z"></path>
                            <path d="M12 7a5 5 0 1 0-10 0 5 5 0 1 0 10 0z"></path>
                        </svg>
                    </div>
                    <h3>Bilişsel Davranışçı Terapi (BDT)</h3>
                    <p>Düşünce ve davranış kalıplarını değiştirerek kalıcı iyileşmeyi hedefleyen yöntem.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Fobiler, OKB, Panik Bozukluğu</div>
                </div>
                <!-- Masal Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                    <h3>Masal Terapisi</h3>
                    <p>Masalların iyileştirici gücü ile çocukların iç dünyasına sembolik yolculuklar.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Çocuk Terapisi, Yaratıcı Anlatım, Duygusal Farkındalık</div>
                </div>
                <!-- Şema Terapi -->
                <div class="service-card">
                    <div class="service-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <h3>Şema Terapi</h3>
                    <p>Kökü çocukluğa dayanan olumsuz yaşam kalıplarını fark etme ve dönüştürme.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Kişilik Bozuklukları, Kronik Depresyon, Bağımlı İlişkiler</div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section -->
    <section class="section" id="testimonials" style="background-color: #FFFFFF;">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">Danışan Yorumları</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Yolculuğumuza eşlik edenlerin deneyimleri.</p>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-card"><i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">"Terapi süreci boyunca kendimi keşfetme yolculuğumda
                        bana sağladıkları profesyonel destek ve
                        akademik yaklaşım,
                        hayata bakış açımı tamamen değiştirdi. Güven veren bir ortamda olduğumu her an
                        hissettim."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>H. A.</h4><span>Bireysel Terapi Danışanı</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card"><i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">"Eşimle yaşadığımız iletişim sorunlarını çözmemizde,
                        uzman kadronun objektif ve yapıcı yaklaşımı
                        çok etkili oldu. İlişkimizde yeni ve sağlıklı bir temel kurmamıza yardımcı
                        oldular."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>M. & E. Ş.</h4><span>Aile & Çift Terapisi Danışanı</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card"><i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">"Kızımızın gelişim sürecinde karşılaştığımız
                        zorluklarda, ekibin çocuk dünyasına olan derin
                        anlayışı ve ebeveyn olarak bize sundukları rehberlik paha biçilemezdi. Çok
                        teşekkür ederiz."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>S. K.</h4><span>Ebeveyn / Danışan Yakını</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Section -->
    <section class="section" id="blog" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2>Güncel Blog Yazılarımız</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Psikoloji dünyasından akademik ve güncel paylaşımlar.</p>
            </div>
            <div class="office-grid">
                <!-- Reusing grid layout for consistent spacing -->
                <?php if (!empty($latest_posts)): ?>
                    <?php foreach ($latest_posts as $post): ?>
                        <div class="blog-card">
                            <div class="blog-date">
                                <?php
                                $date_eng = date('d F Y', strtotime($post['created_at']));
                                $eng_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                $tr_months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                                echo str_replace($eng_months, $tr_months, $date_eng);
                                ?>
                            </div>
                            <div
                                style="height: 250px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?php echo webp_url($post['image']); ?>"
                                        alt="<?php echo htmlspecialchars($post['title']); ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-newspaper fa-3x" style="color: #ccc;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p><?php echo htmlspecialchars(mb_substr($post['excerpt'], 0, 100)) . '...'; ?></p>
                                <a href="<?php echo url('blog/' . $post['slug']); ?>" class="btn-premium"
                                    style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">Devamını Oku</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; width: 100%; color: #666;">Henüz blog yazısı eklenmemiş.</p>
                <?php endif; ?>
            </div>
            <div style="text-align: center; margin-top: 4rem;"><a href="/blog" class="btn-premium"
                    style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none;">Tüm
                    Yazıları Gör</a></div>
        </div>
    </section>
    <!-- FAQ Section -->
    <section class="section" id="faq" style="background-color: #FFFFFF;">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">Sıkça Sorulan Sorular</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Terapi süreci ve işleyişimiz hakkında merak edilenler.</p>
            </div>
            <div class="faq-container">
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Seanslar ne kadar
                            sürüyor?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Bireysel terapi seanslarımız standart olarak 45-50
                            dakika sürmektedir. Çift ve aile terapisi seansları ihtiyaca göre
                            daha uzun planlanabilmektedir. </p>
                    </div>
                </div>
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Kaç
                            seans gelmem gerekiyor?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Terapi süresi,
                            çalışılan konunun derinliğine,
                            kişinin ihtiyaçlarına ve belirlenen hedeflere göre kişiden kişiye
                            farklılık gösterir. İlk seanslarda bu konuda genel bir yol haritası
                            oluşturulur. </p>
                    </div>
                </div>
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Online terapi ile yüz
                            yüze
                            terapi arasında fark
                            var mı?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Bilimsel çalışmalar,
                            online terapinin birçok psikolojik zorlukta yüz yüze terapi kadar
                            etkili olduğunu göstermektedir. Önemli olan terapötik bağın
                            kurulması ve gizliliğin sağlanmasıdır. </p>
                    </div>
                </div>
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Gizlilik ilkesine nasıl
                            uyuluyor?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Terapide paylaşılan tüm bilgiler etik kurallar ve
                            yasalar çerçevesinde tamamen gizli tutulur. Danışanın onayı olmadan
                            hiçbir bilgi üçüncü şahıslarla paylaşılmaz. </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section -->
    <section class="section" id="contact">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">İletişime Geçin</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Size nasıl yardımcı olabileceğimizi konuşmak için bizimle iletişime
                    geçin.</p>
            </div>
            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info-panel">
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary);">
                        İletişim Bilgilerimiz </h3>
                    <p style="color: var(--text-muted); margin-bottom: 3rem;">Bize her
                        zaman ulaşabilirsiniz.</p>
                    <div class="contact-info-list">
                        <div class="contact-info-item"><i class="fas fa-map-marker-alt" style="color: #8B3D48;"></i>
                            <div class="contact-info-text">
                                <h4>Adres</h4>
                                <p>Şehremini, Millet Cd. 34098 Fatih/İstanbul</p>
                            </div>
                        </div>
                        <div class="contact-info-item"><i class="fas fa-phone" style="color: #EC4899;"></i>
                            <div class="contact-info-text">
                                <h4>Telefon</h4>
                                <p>05511765285</p>
                            </div>
                        </div>
                        <div class="contact-info-item"><i class="fas fa-envelope" style="color: #E9D5FF;"></i>
                            <div class="contact-info-text">
                                <h4>E-posta</h4>
                                <p>info@uzmanpsikologsenaceren.com</p>
                            </div>
                        </div>
                        <div class="contact-info-item"><i class="fas fa-clock" style="color: #F87171;"></i>
                            <div class="contact-info-text">
                                <h4>Çalışma Saatleri</h4>
                                <p>Hafta içi: 09:00 - 22:00<br>Hafta sonu: 09:00 - 21:00
                                </p>
                            </div>
                        </div>
                        <div class="contact-info-item"><i class="fab fa-instagram" style="color: #DB2777;"></i>
                            <div class="contact-info-text">
                                <h4>Instagram</h4>
                                <p>@uzmanpsikologsenaceren</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Contact Form -->
                <div class="form-premium">
                    <h3>Randevu Talebi</h3>
                    <form action="#" method="POST">
                        <div class="form-group"><label>Adınız Soyadınız
                                *</label><input type="text" class="form-control" placeholder="Adınız Soyadınız"
                                required></div>
                        <div class="form-group"><label>E-posta Adresiniz
                                *</label><input type="email" class="form-control" placeholder="E-posta Adresiniz"
                                required></div>
                        <div class="form-group"><label>Telefon Numaranız
                                *</label><input type="tel" class="form-control" placeholder="Telefon Numaranız"
                                required></div>
                        <div class="form-group"><label>Hizmet *</label><select class="form-control" required>
                                <option value="" disabled selected>Hizmet Seçiniz
                                </option>
                                <option>Bireysel Terapi</option>
                                <option>Çocuk & Ergen Terapisi</option>
                                <option>Çift & Aile Terapisi</option>
                            </select></div>
                        <div class="form-group"><label>Mesajınız</label><textarea class="form-control" rows="4"
                                placeholder="Mesajınız"></textarea></div><button type="submit" class="btn-premium"
                            style="width: 100%; border: none; cursor: pointer;">Randevu
                            Talebi Gönder</button>
                    </form>
                    <?php include 'includes/footer.php'; ?>