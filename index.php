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

    <!-- Fonts -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Prata&display=swap');

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
        }

        .navbar .logo img {
            height: 70px !important;
            max-height: 70px !important;
            width: auto !important;
            object-fit: contain;
            mix-blend-mode: multiply;
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

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--luxe-bg) 0%, #FFFFFF 100%);
            padding-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-size: clamp(3rem, 8vw, 4.5rem);
            color: var(--primary);
            margin-bottom: 2rem;
        }

        .hero-text h1 span {
            color: var(--secondary);
            display: block;
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
        .services-grid,
        .team-grid,
        .office-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .service-card,
        .team-card,
        .office-card {
            background: var(--white);
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(139, 61, 72, 0.05);
            transition: var(--transition);
            text-align: center;
            overflow: hidden;
        }

        .team-card,
        .office-card {
            padding: 0;
        }

        .card-img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            transition: var(--transition);
        }

        .team-card:hover .card-img,
        .office-card:hover .card-img {
            transform: scale(1.05);
        }

        .card-info {
            padding: 2rem;
        }

        .service-card:hover,
        .team-card:hover,
        .office-card:hover,
        .blog-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--shadow-luxe);
            border-color: var(--secondary);
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
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }

        .float-btn.whatsapp {
            background: #25D366;
        }

        .float-btn.phone {
            background: var(--primary);
        }

        .float-btn:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .float-btn i {
            display: flex !important;
            align-items: center;
            justify-content: center;
            color: white !important;
            font-size: 1.5rem;
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
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 3rem;
            }

            .hero-text h1 {
                font-size: 3rem;
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
                height: 50px !important;
            }

            .hero-text h1 {
                font-size: 2.5rem;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>

<body><!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container"><a href="#" class="logo"><img src="assets/images/logo2026.png"
                    alt="Erguvan Psikoloji Logo" style="max-height: 80px; width: auto;"></a>
            <ul class="nav-links">
                <li><a href="#home">Ana Sayfa</a></li>
                <li><a href="#team">Ekibimiz</a></li>
                <li><a href="#office">Ofisimiz</a></li>
                <li><a href="#services">Hizmetler</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="#contact">İletişim</a></li>
                <li class="nav-phone"><a href="tel:+905511765285"><i class="fas fa-phone-alt"></i>0551 176 52 85</a>
                </li>
            </ul><a href="tel:+905511765285" class="btn-premium"
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
                <div class="hero-btns"><a href="#contact" class="btn-premium">Randevu Al</a><a href="#services"
                        class="btn-premium"
                        style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; margin-left: 1rem;">Hizmetlerimiz</a>
                </div>
            </div>
            <div class="hero-image-wrapper"><img src="assets/images/hero-psikolojik-destek.jpg"
                    alt="Erguvan Psikoloji Ofis"
                    style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg);"></div>
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
                        <p style="color: var(--secondary); font-weight: 600;">Kurucu / Uzman Klinik Psikolog</p>
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
                <div class="office-card"><img src="assets/images/office/ofis-1.jpg" alt="Ofisimiz 1" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;"></div>
                <div class="office-card"><img src="assets/images/office/office2.jpg" alt="Ofisimiz 2" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;"></div>
                <div class="office-card"><img src="assets/images/office/office3.jpg" alt="Ofisimiz 3" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;"></div>
                <div class="office-card"><img src="assets/images/office/office4.jpg" alt="Ofisimiz 4" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;"></div>
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
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Online terapi ile yüz yüze
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
                                <p>Şehremini,
                                    Millet Cd. Aydın apt No:131 Daire 4<br>34098
                                    Fatih/İstanbul</p>
                            </div>
                        </div>
                        <div class="contact-info-item"><i class="fas fa-phone" style="color: #EC4899;"></i>
                            <div class="contact-info-text">
                                <h4>Telefon</h4>
                                <p>0551 176 52 84</p>
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
                </div>
            </div>
        </div>
    </section>
    <footer style="background: var(--primary); color: white; padding: 4rem 0; text-align: center;">
        <div class="container">
            <h2 style="color: white; margin-bottom: 2rem;">ERGUVAN</h2>
            <p style="opacity: 0.7; margin-bottom: 3rem;">Psikoterapi ve Eğitim Merkezi</p>
            <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 3rem;">
                <a href="#"
                    style="color: white; display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 50%;"><svg
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24"
                        fill="currentColor">
                        <path
                            d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.9 0-184.9zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
                    </svg></a><a href="#"
                    style="color: white; display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 50%;"><svg
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="22" height="22"
                        fill="currentColor">
                        <path
                            d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z" />
                    </svg></a>
            </div>
            <p style="font-size: 0.8rem; opacity: 0.5;">© 2026 Erguvan Psikoterapi Merkezi.
                Tüm hakları saklıdır.</p>
        </div>
    </footer>
    <div class="floating-actions">
        <!-- WhatsApp Button --><a href="https://wa.me/905511765285" class="float-btn whatsapp" target="_blank"
            title="WhatsApp ile İletişime Geçin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                width="24" height="24" fill="currentColor">
                <path
                    d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-4-10.5-6.7z" />
            </svg></a>
        <!-- Call Button --><a href="tel:+905511765285" class="float-btn phone" title="Bizi Arayın"><svg
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22" fill="currentColor">
                <path
                    d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.9-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.9 464-464 0-11.2-7.7-20.9-18.6-23.4z" />
            </svg></a>
    </div>
    <script>w        indow.addEventListener('scroll', function () {
                const navbar = document.getElementById('navbar');

                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                }

                else {
                    navbar.classList.remove('scrolled');
                }
            });

        // FAQ Accordion
        document.querySelectorAll('.faq-header').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.parentElement;
                const isActive = item.classList.contains('active');

                // Close all other items
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    otherItem.classList.remove('active');
                });

                // Toggle click item
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>