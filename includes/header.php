<?php
// Config dosyasını yükle
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/seo-helper.php';

// Cache kontrol ve versioning - VERSION sabiti config.php'den geliyor

// SEO değişkenleri (önce tanımla)
$site_name = 'Erguvan Psikoloji';
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$site_url = $protocol . '://' . $host . BASE_URL;
$current_url = $protocol . '://' . $host . $_SERVER['REQUEST_URI'];

// SEO ayarlarını yükle
$seo_data = [];
$google_settings = [];
try {
    $seo_data = getSEOSettings($page ?? 'home');
    $google_settings = getGoogleSettings();
} catch (Exception $e) {
    // Veritabanı hatası durumunda varsayılan değerler
    error_log('SEO/Google settings error: ' . $e->getMessage());
    // Zaten boş array'ler atandı
}

// SEO ayarları varsa kullan
if (!empty($seo_data)) {
    if (!isset($page_title) && !empty($seo_data['meta_title'])) {
        $page_title = $seo_data['meta_title'];
    }
    if (!isset($page_description) && !empty($seo_data['meta_description'])) {
        $page_description = $seo_data['meta_description'];
    }
    if (!isset($page_keywords) && !empty($seo_data['meta_keywords'])) {
        $page_keywords = $seo_data['meta_keywords'];
    }
    if (!isset($page_image) && !empty($seo_data['og_image'])) {
        $page_image = $seo_data['og_image'];
    }
    if (!isset($canonical_url) && !empty($seo_data['canonical_url'])) {
        $canonical_url = $seo_data['canonical_url'];
    }
    if (!isset($robots_content) && !empty($seo_data['robots_content'])) {
        $robots_content = $seo_data['robots_content'];
    }
}

// Varsayılan değerler (Optimizasyon: ~60 karakterlik başlık)
$page_title_full = isset($page_title) ? $page_title . ' - ' . $site_name : $site_name . ' | Fatih Uzman Psikolog & Terapi Merkezi';
$page_description = isset($page_description) ? htmlspecialchars($page_description) : 'Erguvan Psikoloji - Fatih\'te uzman klinik psikolog desteği. Bireysel terapi, oyun terapisi, çift ve aile danışmanlığı ile profesyonel psikolojik yardım.';
$page_keywords = isset($page_keywords) ? htmlspecialchars($page_keywords) : 'psikolog, fatih psikolog, uzman klinik psikolog, oyun terapisi, bireysel terapi, çift terapisi, istanbul psikolog, sena ceren, terapi merkezi';
$logoSvgPathForImage = __DIR__ . '/../assets/images/logo.svg';
$logoWebpPathForImage = __DIR__ . '/../assets/images/logo.webp';
$logoPngPathForImage = __DIR__ . '/../assets/images/logo.png';
$defaultLogo = '';
if (file_exists($logoSvgPathForImage)) {
    $defaultLogo = $site_url . asset_url('images/logo.svg');
} elseif (file_exists($logoWebpPathForImage)) {
    $defaultLogo = $site_url . asset_url('images/logo.webp');
} elseif (file_exists($logoPngPathForImage)) {
    $defaultLogo = $site_url . asset_url('images/logo.png');
}
$page_image = isset($page_image) ? $page_image : $defaultLogo;
$page_type = isset($page_type) ? $page_type : 'website';
$canonical_url = isset($canonical_url) ? $canonical_url : $current_url;
$robots_content = isset($robots_content) ? $robots_content : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

// SEO ayarlarından OG title ve description varsa kullan
if (!empty($seo_data['og_title'])) {
    $page_title_full = $seo_data['og_title'] . ' - ' . $site_name;
}
if (!empty($seo_data['og_description'])) {
    $page_description = htmlspecialchars($seo_data['og_description']);
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!empty($google_settings['google_tag_manager'])): ?>
        <!-- Delayed Google Tag Manager -->
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () {
                    (function (w, d, s, l, i) {
                        w[l] = w[l] || []; w[l].push({
                            'gtm.start':
                                new Date().getTime(), event: 'gtm.js'
                        }); var f = d.getElementsByTagName(s)[0],
                            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                                'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
                    })(window, document, 'script', 'dataLayer', '<?php echo htmlspecialchars($google_settings['google_tag_manager']); ?>');
                }, 3000); // 3-second delay
            });
        </script>
        <!-- End Google Tag Manager -->
    <?php endif; ?>

    <?php if (!empty($google_settings['google_analytics_id'])): ?>
        <!-- Delayed Google Analytics (gtag.js) -->
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () {
                    var script = document.createElement('script');
                    script.async = true;
                    script.src = "https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($google_settings['google_analytics_id']); ?>";
                    document.head.appendChild(script);

                    window.dataLayer = window.dataLayer || [];
                    function gtag() { dataLayer.push(arguments); }
                    gtag('js', new Date());
                    gtag('config', '<?php echo htmlspecialchars($google_settings['google_analytics_id']); ?>', {
                        'page_path': window.location.pathname,
                        'anonymize_ip': true,
                        'content_group': '<?php echo $page ?? "home"; ?>'
                    });
                }, 3500); // 3.5-second delay
            });
        </script>
    <?php endif; ?>


    <!-- Cache Control: Optimized by Antigravity -->
    <!-- Removed no-cache headers for better performance -->


    <?php
    // Meta tag'ler için değişkenler hazır
    ?>

    <!-- Google Site Verification -->
    <meta name="google-site-verification" content="QTKw6xsL_kK9QLvkW2buSvkoeTx4U9xtMyLRkaeM9qE" />

    <!-- Primary Meta Tags -->
    <title><?php echo htmlspecialchars($page_title_full); ?></title>
    <meta name="title" content="<?php echo htmlspecialchars($page_title_full); ?>">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords"
        content="fatih psikolog, istanbul psikolog, uzman klinik psikolog, çocuk psikoloğu fatih, oyun terapisi istanbul, sena ceren, sena ceren parmaksız, sedat parmaksız, psikolojik danışmanlık fatih">
    <meta name="author" content="<?php echo $site_name; ?>">
    <meta name="robots" content="<?php echo $robots_content; ?>">
    <meta name="language" content="Turkish">
    <meta name="revisit-after" content="7 days">
    <meta name="geo.region" content="TR-34">
    <meta name="geo.placename" content="Fatih, İstanbul">
    <meta name="geo.position" content="41.01528;28.93291">
    <meta name="ICBM" content="41.01528, 28.93291">

    <!-- Structured Data (JSON-LD) - SEO Power-up: Optimized for Mental Health Services -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Psychologist",
      "name": "Erguvan Psikoloji",
      "alternateName": "Uzman Klinik Psikolog Desteği",
      "image": "<?php echo htmlspecialchars($page_image); ?>",
      "@id": "<?php echo $site_url; ?>",
      "url": "<?php echo $site_url; ?>",
      "telephone": "+905000000000",
      "priceRange": "₺₺",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "İskenderpaşa Mah. (Örnek Adres)",
        "addressLocality": "Fatih",
        "addressRegion": "İstanbul",
        "postalCode": "34080",
        "addressCountry": "TR"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 41.01528,
        "longitude": 28.93291
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
        ],
        "opens": "09:00",
        "closes": "20:00"
      },
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Psikolojik Danışmanlık Hizmetleri",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Oyun Terapisi"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Bireysel Terapi"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Çocuk ve Ergen Terapisi"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Bilişsel Davranışçı Terapi"
            }
          }
        ]
      },
      "sameAs": [
        "https://www.instagram.com/uzm.psk.senaceren"
      ]
    }
    </script>

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $page_type; ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title_full); ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Uzm. Psk. Sena Ceren - Fatih Uzman Psikolog">
    <meta property="og:site_name" content="<?php echo $site_name; ?>">
    <meta property="og:locale" content="tr_TR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title_full); ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta name="twitter:site" content="@senaceren">

    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#8B5CF6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo $site_name; ?>">

    <!-- Favicon & Touch Icon -->
    <?php
    $logoSvgPathFavicon = __DIR__ . '/../assets/images/logo.svg';
    $logoWebpPathFavicon = __DIR__ . '/../assets/images/logo.webp';
    $logoPngPathFavicon = __DIR__ . '/../assets/images/logo.png';

    if (file_exists($logoSvgPathFavicon)) {
        $faviconUrl = asset_url('images/logo.svg');
        $faviconType = 'image/svg+xml';
    } elseif (file_exists($logoWebpPathFavicon)) {
        $faviconUrl = asset_url('images/logo.webp');
        $faviconType = 'image/webp';
    } else {
        $faviconUrl = asset_url('images/logo.png');
        $faviconType = 'image/png';
    }

    if ($faviconUrl):
        ?>
        <link rel="icon" type="<?php echo $faviconType; ?>" href="<?php echo $faviconUrl; ?>" sizes="any">
        <link rel="apple-touch-icon" href="<?php echo $faviconUrl; ?>" sizes="180x180">
        <link rel="preload" href="<?php echo $faviconUrl; ?>" as="image" fetchpriority="low">
    <?php endif; ?>

    <!-- Preload Critical Assets - LCP Optimization -->
    <link rel="preload" href="<?php echo asset_url('images/logo.webp'); ?>" as="image" fetchpriority="high">
    <?php if (isset($preload_image_mobile) && !empty($preload_image_mobile)): ?>
        <link rel="preload" as="image" href="<?php echo htmlspecialchars($preload_image_mobile); ?>"
            media="(max-width: 600px)" fetchpriority="high">
    <?php endif; ?>
    <?php if (isset($preload_image) && !empty($preload_image)): ?>
        <link rel="preload" as="image" href="<?php echo htmlspecialchars($preload_image); ?>" media="(min-width: 601px)"
            fetchpriority="high">
    <?php endif; ?>

    <!-- Fonts - Critical for LCP & CLS (Uses Local System Fonts/Fallbacks for 0 Layout Shift) -->
    <!-- All external font requests removed for maximum performance -->

    <!-- Critical CSS inline - FCP Optimized -->
    <style>
        /* CSS Variables */
        /* CSS Variables */
        :root {
            --primary: #1D2D50;
            --primary-dark: #0e1628;
            --primary-light: #2c4273;
            --secondary: #8B3D48;
            --accent: #f8fafc;
            --text-dark: #1D2D50;
            --text-medium: #475569;
            --text-light: #94a3b8;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --radius: 16px;
            --shadow: 0 10px 30px rgba(29, 45, 80, 0.1);
            /* Updated Fonts with Fallbacks */
            --font-body: 'Montserrat', 'Montserrat Fallback', system-ui, -apple-system, sans-serif;
            --font-heading: 'Prata', 'Prata Fallback', serif;
        }

        /* Font Metric Overrides - CRITICAL FOR CLS */
        @font-face {
            font-family: 'Prata Fallback';
            src: local('Times New Roman');
            ascent-override: 95%;
            descent-override: 20%;
            line-gap-override: 0%;
            size-adjust: 100%;
        }

        @font-face {
            font-family: 'Montserrat Fallback';
            src: local('Arial');
            ascent-override: 90%;
            descent-override: 25%;
            line-gap-override: 0%;
            size-adjust: 98%;
        }

        /* Base styles */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background: #fff;
            overflow-x: hidden;
            font-size: 0.95rem;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        /* Header - Redesigned with Professional Layout */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            z-index: 1000;
            height: 90px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .header.scrolled {
            height: 75px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo-image {
            height: 65px;
            /* Between 55-70px */
            width: auto;
            object-fit: contain;
            transition: var(--transition);
        }

        .header.scrolled .logo-image {
            height: 55px;
        }

        .logo-text-group {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .logo-text {
            font-size: 1.5rem;
            font-family: var(--font-heading);
            color: var(--primary);
            font-weight: 700;
            margin: 0;
        }

        .logo-subtitle {
            font-size: 0.8rem;
            color: var(--text-medium);
            font-weight: 500;
            font-family: var(--font-body);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .nav-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 0;
            position: relative;
            transition: var(--transition);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: var(--transition);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Right side actions */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .btn-randevu {
            padding: 0.8rem 1.8rem;
            font-size: 0.9rem;
            border-radius: 50px;
            font-weight: 700;
            background: var(--secondary);
            color: white;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(139, 61, 72, 0.2);
            text-decoration: none;
        }

        .btn-randevu:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(139, 61, 72, 0.3);
            filter: brightness(1.1);
        }

        /* Hero Section Sync */
        .hero {
            position: relative;
            min-height: 85vh;
            /* Fallback */
            min-height: 85svh;
            /* Modern */
            display: flex;
            align-items: center;
            padding-top: 90px;
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            overflow: hidden;
            box-sizing: border-box;

            /* Static Hero Styles (v25) */
        }

        .hero-static {
            width: 100%;
            height: 100%;
            min-height: 85vh;
            /* Fallback */
            min-height: 85svh;
            /* Modern */
            display: flex;
            align-items: center;
        }

        .hero-image-wrapper {
            background: #fdf2f8;
            border-radius: var(--radius);
            overflow: hidden;
            aspect-ratio: 600 / 411;
            will-change: transform;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .nav-menu {
                gap: 1.2rem;
            }

            .logo-text {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            .header {
                height: 70px;
            }

            .header.scrolled {
                height: 70px;
            }

            .logo-image {
                height: 50px;
            }

            .logo-text-group {
                display: none;
                /* Hide text on small mobile for space */
            }

            .nav-toggle {
                display: flex;
            }

            .nav-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 280px;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding: 100px 2rem 2rem;
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                align-items: flex-start;
                z-index: 1100;
            }

            .nav-menu.active {
                right: 0;
            }

            .nav-menu ul {
                width: 100%;
            }

            .hero {
                padding-top: 130px;
                /* Fixed spacing from top */
                min-height: 85vh !important;
                /* Fallback */
                min-height: 85svh !important;
                /* Modern */
                /* Fixed CLS: STRICT containment */
                contain: layout size !important;
                display: flex;
                flex-direction: column;
                justify-content: flex-start !important;
                /* Anchor to TOP */
            }

            /* No need to redefine min-height for hero-slider, simplified */
        }

        /* Composited Animations: Use transform/opacity only */
        .service-icon,
        .btn,
        .nav-toggle,
        .testimonials-nav {
            will-change: transform, opacity;
        }
    </style>

    <!-- Main CSS (Deferred Loading) -->
    <?php
    $minCssPath = __DIR__ . '/../assets/css/style.min.css';
    $cssFile = file_exists($minCssPath) ? 'style.min.css' : 'style.css';
    ?>
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.min.css'); ?>">
    <!-- <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>"> -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars(css_url($cssFile)); ?>" media="print"
        onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="<?php echo htmlspecialchars(css_url($cssFile)); ?>">
    </noscript>

    <!-- Sürüm bazlı çerez sıfırlama mekanizması -->
    <script>
        (function () {
            var currentVersion = '<?php echo defined('VERSION') ? VERSION : 'v2'; ?>';
            var storedVersion = localStorage.getItem('site_version');

            if (storedVersion !== currentVersion) {
                document.cookie.split(";").forEach(function (c) {
                    var eqPos = c.indexOf("=");
                    var name = eqPos > -1 ? c.substr(0, eqPos).trim() : c.trim();
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=" + window.location.hostname;
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=." + window.location.hostname;
                });

                var keysToKeep = ['site_version'];
                for (var i = localStorage.length - 1; i >= 0; i--) {
                    var key = localStorage.key(i);
                    if (key && keysToKeep.indexOf(key) === -1) {
                        localStorage.removeItem(key);
                    }
                }

                localStorage.setItem('site_version', currentVersion);

                if (storedVersion !== null) {
                    window.location.reload(true);
                }
            }
        })();
    </script>
</head>

<body>
    <?php if (!empty($google_settings['google_tag_manager'])): ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe
                src="https://www.googletagmanager.com/ns.html?id=<?php echo htmlspecialchars($google_settings['google_tag_manager']); ?>"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php endif; ?>
    <header class="header" id="mainHeader">
        <div class="container">
            <nav class="nav">
                <a href="<?php echo url(); ?>" class="logo" aria-label="Erguvan Psikoloji Ana Sayfa">
                    <img src="<?php echo asset_url('images/logo.webp'); ?>" alt="Erguvan Psikoloji" class="logo-image"
                        style="height: 70px; width: auto;">
                </a>
                <div class="nav-menu" id="navMenu">
                    <a href="<?php echo url(); ?>" class="nav-link <?php echo $page == 'home' ? 'active' : ''; ?>">Ana
                        Sayfa</a>
                    <a href="<?php echo url('#hizmetler'); ?>" class="nav-link">Hizmetlerimiz</a>
                    <a href="<?php echo url('#hakkimizda'); ?>" class="nav-link">Hakkımızda</a>
                    <a href="<?php echo url('#ekibimiz'); ?>" class="nav-link">Ekibimiz</a>
                    <a href="<?php echo page_url('blog.php'); ?>"
                        class="nav-link <?php echo $page == 'blog' ? 'active' : ''; ?>">Blog</a>
                    <a href="<?php echo url('#iletisim'); ?>" class="nav-link">İletişim</a>
                </div>
                <div class="nav-actions">
                    <a href="<?php echo url('#randevu'); ?>" class="btn-randevu">Randevu Al</a>
                    <button class="nav-toggle" id="navToggle" aria-label="Menüyü aç/kapat" aria-expanded="false"
                        aria-controls="navMenu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </nav>
        </div>
    </header>