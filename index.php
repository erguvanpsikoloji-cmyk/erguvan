<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

// Orijinal Danışan Yorumları - Google Reviews (v77)
$testimonials = [
    [
        'name' => 'Nihat Duman',
        'comment' => 'Sena Hanım\'a tavsiye üzerine gittim, ilgi ve alakası çok iyi. Gerçek bir psikolog arıyorsanız doğru adrestesiniz.',
        'rating' => 5,
        'date_info' => 'Bir ay önce',
        'avatar_char' => 'N'
    ],
    [
        'name' => 'Ayşe Yılmaz',
        'comment' => 'Sena Hanım\'ı kesinlikle tavsiye ederim. Çok anlayışlı ve profesyonel. Online terapi seansları çok verimli geçti.',
        'rating' => 5,
        'date_info' => '3 ay önce',
        'avatar_char' => 'A'
    ],
    [
        'name' => 'A. Kaya',
        'comment' => 'Hayatımın en zor döneminde Sena Hanım ile tanıştım. Profesyonel ve samimi yaklaşımı sayesinde kendimi yeniden buldum.',
        'rating' => 5,
        'date_info' => '2 ay önce',
        'avatar_char' => 'A'
    ],
    [
        'name' => 'M. Yılmaz',
        'comment' => 'Ferah ofis ortamı ve güven veren duruşuyla terapi sürecim çok verimli geçti. Herkese tavsiye ediyorum.',
        'rating' => 5,
        'date_info' => '4 ay önce',
        'avatar_char' => 'M'
    ],
    [
        'name' => 'Selin Arslan',
        'comment' => 'Sena Hanım ile çalışmak hayatımda çok önemli bir adım oldu. Sabırlı, anlayışlı ve oldukça deneyimli bir terapist. Her seanstan motive ayrılıyorum.',
        'rating' => 5,
        'date_info' => '5 ay önce',
        'avatar_char' => 'S'
    ],
    [
        'name' => 'Kemal Çelik',
        'comment' => 'Sedat Bey ile yaptığım görüşmeler sayesinde iş hayatındaki stresimi çok daha iyi yönetmeye başladım. Profesyonel ve çözüm odaklı bir yaklaşımı var.',
        'rating' => 5,
        'date_info' => '6 ay önce',
        'avatar_char' => 'K'
    ],
    [
        'name' => 'Elif Bozkurt',
        'comment' => 'Panik atak tedavisinde gerçekten büyük ilerleme kaydettim. Sena Hanım hem anlayışlı hem de çok bilgili. Terapi sürecinde hiç yalnız hissetmedim.',
        'rating' => 5,
        'date_info' => '7 ay önce',
        'avatar_char' => 'E'
    ],
    [
        'name' => 'Oğuz Demir',
        'comment' => 'Çok anlayışlı ve güven verici bir ortam. İlk seanstan itibaren kendimi rahat hissettim. Kesinlikle tavsiye ediyorum.',
        'rating' => 5,
        'date_info' => '8 ay önce',
        'avatar_char' => 'O'
    ]
];
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

    <!-- Critical Rendering Path - Optimised Resources -->
    <!-- Google Fonts - Asynchronous Loading (Zero Render-Blocking) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;500;600&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;500;600&display=swap"
            rel="stylesheet">
    </noscript>


    <!-- Hero Image Responsive Preload - Split for accurate LCP Discovery -->
    <link rel="preload" as="image" href="<?php echo webp_url('assets/images/hero-psikolojik-destek-mobile.webp'); ?>"
        media="(max-width: 600px)" fetchpriority="high">
    <link rel="preload" as="image" href="<?php echo webp_url('assets/images/hero-psikolojik-destek-opt.webp'); ?>"
        media="(min-width: 601px)" fetchpriority="high">

    <!-- Swiper CSS - Deferred -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" media="print"
        onload="this.media='all'">

    <!-- Non-Critical Styles - Deferred -->
    <link rel="stylesheet" href="<?php echo css_url('main.css'); ?>" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="<?php echo css_url('main.css'); ?>">
    </noscript>

    <style>
        :root {
            /* Corporate Color Palette (Logo Aligned) */
            --primary: #0F172A;
            --secondary: #915F78;
            --accent: #A66C8E;
            --accent-soft: #FDF2F8;
            --luxe-bg: #FAF5FF;
            --nav-bg: #fafafa;
            /* Minimal cream tone */
            --text-dark: #0F172A;
            --text-muted: #475569;
            --white: #FFFFFF;

            /* Design Tokens */
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Inter', sans-serif;
            --transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-lg: 40px;
            --radius-md: 20px;
            --shadow-premium: 0 2px 15px rgba(0, 0, 0, 0.03);
            --shadow-button: 0 4px 12px rgba(145, 95, 120, 0.2);
            --glass: rgba(250, 250, 250, 0.95);
            --grad-corporate: linear-gradient(135deg, #915F78 0%, #70475E 100%);
        }

        /* Base Styles - Critical */
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

        /* Typography - Critical */
        h1,
        h2,
        h3 {
            font-family: var(--font-heading);
            font-weight: 400;
            line-height: 1.2;
        }

        /* Navigation - Critical */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10000;
            height: 70px !important;
            /* Refined height */
            background: var(--nav-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: var(--shadow-premium);
            transition: var(--transition);
            display: flex;
            align-items: center;
        }

        .navbar.scrolled {
            height: 70px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .navbar .logo {
            display: flex !important;
            align-items: center !important;
            text-decoration: none !important;
            gap: 1rem;
            /* Balanced gap */
        }

        .navbar .logo img {
            height: 60px !important;
            max-height: 60px !important;
            min-height: 60px !important;
            /* v72-v73 Stability */
            width: auto !important;
            object-fit: contain;
            background: transparent !important;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            min-width: 180px;
        }

        .logo-title {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            /* Refined size for balance */
            color: var(--primary);
            letter-spacing: -0.5px;
            font-weight: 500;
            /* Slightly more defined */
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
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .cta-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
            align-items: center;
            justify-content: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.8px;
            /* Premium letter spacing */
            padding: 10px 5px;
            white-space: nowrap;
            transition: var(--transition);
            position: relative;
            text-transform: uppercase;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0px;
            /* v72 Closer distance */
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: var(--transition);
        }

        .nav-links img {
            display: none;
        }

        .mobile-only {
            display: none !important;
        }

        .nav-phone {
            display: none !important;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-premium.head-cta {
            padding: 10px 22px;
            font-size: 0.85rem;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow-button);
            margin-left: 0;
        }

        .btn-premium.head-cta:hover {
            filter: brightness(0.9);
            transform: translateY(-2px);
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            z-index: 10001 !important;
            padding: 10px;
        }

        .menu-toggle span {
            display: block;
            width: 30px;
            height: 3px;
            background: var(--primary) !important;
            border-radius: 3px;
            transition: var(--transition);
        }

        /* Hero Section - Critical */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--luxe-bg) 0%, #FFFFFF 100%);
            padding-top: 120px;
            position: relative;
            overflow: visible !important;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 5rem;
            align-items: center;
            overflow: visible !important;
        }

        .hero-text {
            order: 1;
        }

        .hero-image-wrapper {
            order: 2;
        }

        .hero-subtitle {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: rgba(145, 95, 120, 0.08);
            color: var(--secondary);
            border-radius: 100px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .hero-text h1 {
            font-size: clamp(3rem, 8vw, 4.5rem);
            color: var(--primary);
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        .hero-text h1 {
            font-size: clamp(3rem, 8vw, 4.5rem);
            color: var(--primary);
            margin-bottom: 1.5rem;
            line-height: 1.1;
            /* Tighter line-height for stability */
        }

        .hero-text h1 span {
            color: var(--secondary);
            display: block;
            margin-top: 0.2rem;
        }

        .hero-text p {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 500px;
            line-height: 1.6;
        }

        .hero-subtitle i {
            width: 1.2rem;
            height: 1.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }

        .btn-premium {
            display: inline-block;
            padding: 12px 26px;
            /* Optimized padding */
            background: var(--grad-corporate);
            color: var(--white);
            text-decoration: none;
            border-radius: 30px;
            /* More rounded premium feel */
            font-weight: 700;
            font-size: 0.95rem;
            transition: var(--transition);
            box-shadow: 0 6px 18px rgba(145, 95, 120, 0.25);
            /* Refined shadow */
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-image-wrapper {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-luxe);
            aspect-ratio: 1024 / 768;
            background: rgba(145, 95, 120, 0.05);
        }

        .hero-image-wrapper img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: var(--radius-lg);
            display: block;
            transition: var(--transition);
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        /* Floating Contact - Classic Round Buttons */
        .floating-contact {
            position: fixed;
            bottom: 25px;
            right: 25px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 9999;
            width: 50px;
            height: 112px;
        }

        .fc-classic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }

        .fc-classic.wa {
            background: #25D366;
        }

        .fc-classic.ph {
            background: #1D2D50;
        }

        .fc-classic:hover {
            transform: scale(1.1);
        }

        /* Media Queries - Critical */
        @media (max-width: 900px) {
            .navbar {
                padding: 0 !important;
                height: 75px !important;
                min-height: 75px !important;
                display: flex;
                align-items: center;
            }

            .nav-container {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0 16px !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .cta-wrapper {
                display: none !important;
            }

            .navbar .logo img {
                height: 44px !important;
                max-height: 44px !important;
                width: auto !important;
                object-fit: contain;
                margin: 0 !important;
            }

            .logo-text {
                margin-left: 12px !important;
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 2px;
            }

            .logo-title {
                font-size: 1.15rem !important;
                line-height: 1.1 !important;
                margin: 0 !important;
            }

            .logo-subtitle {
                font-size: 0.65rem !important;
                line-height: 1.1 !important;
                margin: 0 !important;
            }

            .hero {
                padding-top: 95px !important;
                min-height: 600px !important;
                /* Force space for LCP image */
                padding-bottom: 4rem;
            }

            .hero-content {
                grid-template-columns: 1fr !important;
                gap: 2.5rem !important;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2.6rem !important;
                margin-bottom: 1.5rem !important;
                min-height: 120px;
                /* Reserve space for 2-3 lines */
                display: block;
            }

            /* Critical Inlined Styles */
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

            .hero-ps-text {
                font-size: 1.4rem !important;
                margin-bottom: 1.5rem !important;
                min-height: 50px;
                /* Space for single line title */
                display: block;
                line-height: 1.3 !important;
                font-weight: 600 !important;
            }

            .hero-desc-text {
                font-size: 1.1rem !important;
                margin-bottom: 2rem !important;
                display: block;
                line-height: 1.5 !important;
            }

            .hero-image-wrapper {
                aspect-ratio: 4 / 3 !important;
                margin-bottom: 2rem;
                order: -1 !important;
                /* Strictly first on mobile */
            }

            .menu-toggle {
                display: flex !important;
                flex-direction: column;
                gap: 5px;
                width: 44px;
                height: 44px;
                align-items: center;
                justify-content: center;
                position: relative;
                z-index: 10001 !important;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 8px;
            }

            .menu-toggle span {
                width: 25px !important;
                height: 3px !important;
                background: var(--primary) !important;
            }

            .nav-links {
                display: none;
                /* JS will toggle this */
            }
        }
    </style>
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</head>

<body>
    <!-- Content Started (JS deferred at bottom) -->
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-wrapper">
                <a href="#" class="logo">
                    <img src="<?php echo webp_url('assets/images/logo_erguvan_transparent.png'); ?>"
                        alt="Erguvan Psikoloji Logo" width="250"
                        style="height: 60px !important; width: auto; max-width: 100%; object-fit: contain; background: transparent !important;">
                </a>
            </div>


            <div class="menu-toggle" id="navToggle"><span></span><span></span><span></span></div>

            <ul class="nav-links" id="navMenu">
                <li><a href="#home" class="nav-link">Ana Sayfa</a></li>
                <li><a href="#team" class="nav-link">Ekibimiz</a></li>
                <li><a href="pages/ofisimiz.php" class="nav-link">Ofisimiz</a></li>
                <li><a href="#hizmetler" class="nav-link">Hizmetler</a></li>
                <li><a href="#blog" class="nav-link">Blog</a></li>
                <li><a href="#contact" class="nav-link">İletişim</a></li>
                <li class="mobile-only"><a href="tel:+905511765285" class="btn-premium"
                        style="padding: 1rem 2rem; width: 100%; text-align: center;">Hemen Ara</a></li>
            </ul>

            <div class="cta-wrapper">
                <a href="tel:+905511765285" class="btn-premium head-cta">
                    <i class="fas fa-phone-alt" style="font-size: 0.8rem;"></i>
                    Hemen Ara
                </a>
            </div>
        </div>
    </nav>
    </nav>
    <!-- Hero Section -->
    <header class="hero" id="home">
        <div class="container hero-content">
            <div class="hero-image-wrapper">
                <picture>
                    <source srcset="<?php echo webp_url('assets/images/hero-psikolojik-destek-mobile.webp'); ?>"
                        media="(max-width: 600px)" type="image/webp">
                    <source srcset="<?php echo webp_url('assets/images/hero-psikolojik-destek-opt.webp'); ?>"
                        type="image/webp">
                    <img src="<?php echo webp_url('assets/images/hero-psikolojik-destek-opt.jpg'); ?>"
                        alt="Erguvan Psikoloji Modern Ofis" fetchpriority="high" width="1024" height="768"
                        loading="eager">
                </picture>
            </div>
            <div class="hero-text">
                <div class="hero-subtitle"><svg class="svg-icon" viewBox="0 0 576 512"
                        style="width:18px; height:18px; margin-right:10px; fill:currentColor;">
                        <path
                            d="M547.3 123.6c-48.4-53.1-125.7-72.2-200.2-56.5-7.7 1.6-15.1 3.5-22.1 5.8C280.4 21.6 247.9 0 208 0 139.7 0 91.6 42.4 81.3 104.9c-1.7 8.3-3.1 16.6-4.2 24.9C36.3 155.6 0 220.4 0 288c0 106 86 192 192 192 58.7 0 111.7-26.7 147-68.1 27.2 20.3 59.4 33.2 95 33.2 55.9 0 103.9-35 123.3-84.2 1.1-2.9 2.1-5.9 3-8.8 53.6-13.6 94-63.5 94-121.2 0-66.7-53.8-121.2-120.7-123.6zM192 416c-53 0-96-43-96-96 0-26.7 10.8-51.2 28.3-69.5l1.8-1.8 45.4 45.4L165 344.8c-4.2 4.2-6.8 9.6-6.8 15.2 0 11.2 9.1 20.3 20.3 20.3 11.2 0 20.3-9.1 20.3-20.3 0-5.6-2.6-11-6.8-15.2l-23.7-23.7 30.6-30.6 23.7 23.7c4.2 4.2 9.6 6.8 15.2 6.8 11.2 0 20.3-9.1 20.3-20.3 0-5.6-2.6-11-6.8-15.2L201 229.2c-1.8-1.8-3.3-4-4.5-6.3-5.5-10.9-8.5-23.3-8.5-36.5 0-35.3 28.7-64 64-64h1.7l-1.8-1.8c18.3-17.5 42.8-28.3 69.5-28.3 53 0 96 43 96 96 0 26.7-10.8 51.2-28.3 69.5l-1.8 1.8-45.4-45.4L379 176.2c4.2-4.2 6.8-9.6 6.8-15.2 0-11.2-9.1-20.3-20.3-20.3-11.2 0-20.3 9.1-20.3 20.3 0 5.6 2.6 11 6.8 15.2l23.7 23.7-30.6 30.6-23.7-23.7c-4.2-4.2-9.6-6.8-15.2-6.8-11.2 0-20.3 9.1-20.3 20.3 0 5.6 2.6 11 6.8 15.2L311 286.8c1.8 1.8 3.3 4 4.5 6.3 5.5 10.9 8.5 23.3 8.5 36.5 0 35.3-28.7 64-64 64h-1.7l1.8 1.8c-18.3 17.5-42.8 28.3-69.5 28.3zM448 352c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z" />
                    </svg>Birlikte, Daha İyiye. </div>
                <h1>Erguvan <span>Psikoloji</span></h1>
                <p class="hero-ps-text" style="color: var(--primary); font-family: var(--font-body);">
                    Uzman Klinik Psikolog Desteği </p>
                <p class="hero-desc-text">Modern bilimin ışığında,
                    insan ruhunun derinliklerine saygı duyan bir yaklaşımla profesyonel psikoterapi hizmeti
                    sunuyoruz. </p>
                <div class="hero-btns"><a href="#contact" class="btn-premium">Randevu Al</a><a href="#hizmetler"
                        class="btn-premium"
                        style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; margin-left: 1rem;">Hizmetlerimiz</a>
                </div>
            </div>
        </div>
    </header>
    <!-- Team Section -->
    <section class="section premium-team-section" id="team">
        <div class="container">
            <div class="section-title">
                <h2>Uzman Ekibimiz</h2>
                <p>Akademik birikim ve klinik tecrübeyi harmanlayan profesyonel kadromuz.</p>
                <div class="title-line-premium"></div>
            </div>
            <div class="team-grid">
                <!-- Sena Ceren Parmaksız -->
                <div class="premium-team-card">
                    <div class="card-image-wrapper"><img src="assets/images/team/sena.webp"
                            alt="Uzm. Klinik Psk. Sena Ceren Parmaksız" loading="lazy" width="400" height="500"
                            style="object-fit: cover; width: 100%; height: 100%;"></div>
                    <div class="card-content">
                        <h3>Sena Ceren Parmaksız</h3>
                        <p>Uzman Klinik Psikolog</p>
                    </div>
                </div>
                <!-- Sedat Parmaksız -->
                <div class="premium-team-card">
                    <div class="card-image-wrapper"><img src="assets/images/team/sedat.webp"
                            alt="Uzm. Klinik Psk. Sedat Parmaksız" loading="lazy" width="400" height="500"
                            style="object-fit: cover; width: 100%; height: 100%;"></div>
                    <div class="card-content">
                        <h3>Sedat Parmaksız</h3>
                        <p>Uzman Klinik Psikolog</p>
                    </div>
                </div>
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
                    <div class="service-icon-wrapper"><i data-lucide="user"></i></div>
                    <h3>Bireysel Terapi</h3>
                    <p>Kendinizi anlama ve hayat kalitenizi artırma yolculuğunda yanınızdayız.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Depresyon,
                        Kaygı Bozuklukları,
                        Travma</div>
                </div>
                <!-- Aile ve Çift Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><i data-lucide="users"></i></div>
                    <h3>Aile ve Çift Terapisi</h3>
                    <p>İlişkilerinizde daha sağlıklı iletişim ve güçlü bağlar kurmanız için
                        yanınızdayız.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Aile İçi Çatışmalar,
                        İletişim Sorunları,
                        Boşanma Süreci</div>
                </div>
                <!-- Oyun Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><i data-lucide="rocking-chair"></i></div>
                    <h3>Oyun Terapisi</h3>
                    <p>Çocukların kendilerini ifade etme dili olan oyun ile duygusal
                        iyileşme sağlıyoruz.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Davranış Sorunları,
                        Ayrılık Kaygısı,
                        Yas</div>
                </div>
                <!-- Yetişkin Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><i data-lucide="brain"></i></div>
                    <h3>Yetişkin Terapisi</h3>
                    <p>Yetişkinlik döneminin getirdiği zorluklarla başa çıkmak için
                        profesyonel destek.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Sınav Kaygısı,
                        Özgüven,
                        Kariyer Stresi</div>
                </div>
                <!-- Çocuk Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><i data-lucide="smile"></i></div>
                    <h3>Çocuk Terapisi</h3>
                    <p>Çocukların gelişimsel süreçlerinde karşılaştıkları güçlükleri
                        birlikte aşıyoruz.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Dikkat Dağınıklığı,
                        Uyum Sorunları,
                        Korku ve Fobiler</div>
                </div>
                <!-- Ebeveyn Danışmanlığı -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><i data-lucide="heart-handshake"></i></div>
                    <h3>Ebeveyn Danışmanlığı</h3>
                    <p>Ebeveynlik yolculuğunda karşılaşılan sorulara bilimsel
                        cevaplar ve rehberlik.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Sınır Koyma,
                        Bağlanma Stilleri,
                        Ergenlik Dönemi</div>
                </div>
                <!-- Bilişsel Davranışçı Terapi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><i data-lucide="sparkles"></i></div>
                    <h3>Bilişsel Davranışçı Terapi (BDT)</h3>
                    <p>Düşünce ve davranış kalıplarını değiştirerek kalıcı
                        iyileşmeyi hedefleyen yöntem.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Fobiler,
                        OKB,
                        Panik Bozukluğu</div>
                </div>
                <!-- Masal Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z">
                            </path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z">
                            </path>
                        </svg></div>
                    <h3>Masal Terapisi</h3>
                    <p>Masalların iyileştirici gücü ile çocukların iç
                        dünyasına sembolik yolculuklar.</p>
                    <div class="service-focus-label">Odak Alanları:
                    </div>
                    <div class="service-focus-areas">Çocuk Terapisi,
                        Yaratıcı Anlatım,
                        Duygusal Farkındalık</div>
                </div>
                <!-- Şema Terapi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z">
                            </path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg></div>
                    <h3>Şema Terapi</h3>
                    <p>Kökü çocukluğa dayanan olumsuz yaşam
                        kalıplarını fark etme ve dönüştürme.</p>
                    <div class="service-focus-label">Odak Alanları:
                    </div>
                    <div class="service-focus-areas">Kişilik
                        Bozuklukları,
                        Kronik Depresyon,
                        Bağımlı İlişkiler</div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section v77 -->
    <section class="section" id="testimonials" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">Danışanlarımın Kaleminden</h2>
                <div class="title-line-premium" style="margin: 0 auto 1rem;"></div>
                <p>Google üzerinden paylaşılan gerçek danışan deneyimleri.</p>
            </div>

            <!-- Google badge -->
            <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:2.5rem;">
                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#4285F4" d="M44.5 20H24v8.5h11.9C34.5 33.1 30.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l6-6C34.5 6.5 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c11 0 20-8 20-20 0-1.3-.1-2.7-.5-4z"/><path fill="#34A853" d="M7 26.5l-6.3 4.9C2.5 35.7 5.9 40 10 43l6-6c-2.2-1.5-3.8-3.7-4.3-6.3z" transform="translate(1,-2)"/><path fill="#FBBC05" d="M24 44c5.5 0 10.2-1.8 13.6-4.9L31.9 33c-1.9 1.3-4.2 2-7.9 2-6.3 0-10.5-2.9-11.9-7.5L5.7 32.4C8.9 39.2 15.9 44 24 44z" transform="translate(-1,0)"/><path fill="#EA4335" d="M44.5 20H24v8.5h11.9c-.9 2.6-2.7 4.7-5.1 6.1l5.7 5.7C40.2 37.2 44 31.2 44 24c0-1.3-.2-2.7-.5-4z" transform="translate(0,-1)"/></svg>
                <span style="font-size:0.9rem; color:var(--text-muted); font-weight:600;">Google Yorumları</span>
                <span style="display:flex; gap:2px;"><?php for($i=0;$i<5;$i++): ?><svg width="16" height="16" viewBox="0 0 24 24" fill="#FBBC05"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg><?php endfor; ?></span>
                <span style="font-size:0.85rem; color:var(--text-muted);">5.0 / 5</span>
            </div>

            <div class="reviews-grid" id="reviewsGrid">
                <?php foreach ($testimonials as $i => $testimonial): ?>
                    <div class="review-card<?php echo $i >= 3 ? ' review-hidden' : ''; ?>">
                        <!-- Stars -->
                        <div class="review-stars">
                            <?php for($s=0; $s<$testimonial['rating']; $s++): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#FBBC05"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <?php endfor; ?>
                        </div>
                        <!-- Comment -->
                        <p class="review-text">"<?php echo htmlspecialchars($testimonial['comment']); ?>"</p>
                        <!-- Author -->
                        <div class="review-author">
                            <div class="review-avatar"><?php echo htmlspecialchars($testimonial['avatar_char']); ?></div>
                            <div>
                                <strong class="review-name">— <?php echo htmlspecialchars($testimonial['name']); ?></strong>
                                <span class="review-date"><?php echo htmlspecialchars($testimonial['date_info']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Daha Fazlası Butonu -->
            <?php if (count($testimonials) > 3): ?>
            <div style="text-align:center; margin-top:2.5rem;">
                <button id="showMoreReviews" onclick="showAllReviews()" class="btn-premium" style="background:transparent; color:var(--secondary); border:2px solid var(--secondary); box-shadow:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                    Daha Fazlası
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <style>
    .reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .review-card {
        background: #fff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 2px 20px rgba(145,95,120,0.08);
        border: 1px solid rgba(145,95,120,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .review-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(145,95,120,0.15);
    }
    .review-hidden {
        display: none;
    }
    .review-hidden.reveal {
        display: flex;
        animation: reviewFadeIn 0.4s ease forwards;
    }
    @keyframes reviewFadeIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .review-stars { display: flex; gap: 2px; }
    .review-text {
        font-size: 0.96rem;
        color: var(--text-muted);
        line-height: 1.7;
        font-style: italic;
        flex: 1;
    }
    .review-author {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: auto;
    }
    .review-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary), var(--accent));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .review-name {
        display: block;
        font-size: 0.95rem;
        color: var(--primary);
    }
    .review-date {
        display: block;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 2px;
    }
    @media (max-width: 900px) {
        .reviews-grid { grid-template-columns: 1fr; }
    }
    @media (min-width: 601px) and (max-width: 900px) {
        .reviews-grid { grid-template-columns: repeat(2, 1fr); }
    }
    </style>
    <script>
    function showAllReviews() {
        const hidden = document.querySelectorAll('.review-hidden');
        hidden.forEach(function(card, i) {
            card.classList.add('reveal');
            card.style.animationDelay = (i * 0.1) + 's';
        });
        document.getElementById('showMoreReviews').style.display = 'none';
    }
    </script>
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
                            <div class="blog-img-container">
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?php echo webp_url($post['image']); ?>"
                                        alt="<?php echo htmlspecialchars($post['title']); ?>" width="400" height="250"
                                        loading="lazy">
                                <?php else: ?>
                                    <i class="fas fa-newspaper fa-3x"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p><?php echo htmlspecialchars(mb_substr($post['excerpt'], 0, 100)) . '...'; ?>
                                </p>
                                <div style="text-align: center;">
                                    <a href="<?php echo url('blog/' . $post['slug']); ?>" class="btn-premium"
                                        style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">Devamını Oku</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; width: 100%; color: #666;">Henüz blog yazısı
                        eklenmemiş.</p>
                <?php endif; ?>
            </div>
            <div style="text-align: center; margin-top: 4rem;"><a href="/blog" class="btn-premium"
                    style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none;">Tüm
                    Yazı
                    ları
                    Gör</a></div>
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
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Seanslar ne
                            kadar
                            sürüyor?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Bireysel terapi seanslarımız standart olarak
                            45-50 dakika sürmektedir. Çift ve aile terapisi seansları
                            ihtiyaca göre daha uzun planlanabilmektedir. </p>
                    </div>
                </div>
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Kaç seans
                            gelmem
                            gerekiyor?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Terapi süresi,
                            çalışılan konunun derinliğine,
                            kişinin ihtiyaçlarına ve belirlenen hedeflere göre kişiden
                            kişiye farklılık gösterir. İlk seanslarda bu konuda genel bir
                            yol haritası oluşturulur. </p>
                    </div>
                </div>
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Online
                            terapi ile yüz yüze
                            terapi arasında
                            fark var mı?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Bilimsel çalışmalar,
                            online terapinin birçok psikolojik zorlukta yüz yüze terapi
                            kadar etkili olduğunu göstermektedir. Önemli olan terapötik
                            bağın kurulması ve gizliliğin sağlanmasıdır. </p>
                    </div>
                </div>
                <div class="faq-item"><button class="faq-header"><span class="faq-question">Gizlilik
                            ilkesine nasıl
                            uyuluyor?</span><i class="fas fa-plus faq-icon"></i></button>
                    <div class="faq-content">
                        <p class="faq-answer">Terapide paylaşılan tüm bilgiler etik kurallar
                            ve yasalar çerçevesinde tamamen gizli tutulur. Danışanın onayı
                            olmadan hiçbir bilgi üçüncü şahıslarla paylaşılmaz. </p>
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
                <p>Size nasıl yardımcı olabileceğimizi konuşmak için bizimle iletişime geçin.</p>
            </div>
            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info-panel">
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary);">
                        İletişim Bilgilerimiz </h3>
                    <p style="color: var(--text-muted); margin-bottom: 3rem;">Bize
                        her zaman ulaşabilirsiniz.</p>
                    <div class="contact-info-list">
                        <div class="contact-info-item"><i class="fas fa-map-marker-alt" style="color: #8B3D48;"></i>
                            <div class="contact-info-text">
                                <h4>Adres</h4>
                                <p>Şehremini,
                                    Millet Cd. 34098 Fatih/İstanbul</p>
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
                                <p>Hafta içi: 09:00 - 22:00<br>Hafta sonu: 09:00 -
                                    21:00 </p>
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
                                <option value="" disabled selected>Hizmet
                                    Seçiniz </option>
                                <option>Bireysel Terapi</option>
                                <option>Çocuk & Ergen Terapisi</option>
                                <option>Çift & Aile Terapisi</option>
                            </select></div>
                        <div class="form-group">
                            <label>Mesajınız</label><textarea class="form-control" rows="4"
                                placeholder="Mesajınız"></textarea>
                        </div><button type="submit" class="btn-premium"
                            style="width: 100%; border: none; cursor: pointer;">Randevu
                            Talebi Gönder</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>