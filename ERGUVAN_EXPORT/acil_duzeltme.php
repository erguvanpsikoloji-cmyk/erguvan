<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Acil Onarım Başlatılıyor...</h1>";

$header_content = <<<'EOD'
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
$page_keywords = isset($page_keywords) ? htmlspecialchars($page_keywords) : 'psikolog, fatih psikolog, uzman klinik psikolog, oyun terapisi, bireysel terapi, çift terapisi, istanbul psikolog, erguvan psikoloji, terapi merkezi';
$logoWebpPathForImage = __DIR__ . '/../assets/images/logo.webp';
$logoPngPathForImage = __DIR__ . '/../assets/images/logo.png';
$defaultLogo = '';
if (file_exists($logoWebpPathForImage)) {
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


    <!-- Cache Control: Optimized by Antigravity -->
    <!-- Removed no-cache headers for better performance -->


    <?php
    // Meta tag'ler için değişkenler hazır
    ?>

    <!-- Primary Meta Tags -->
    <title><?php echo htmlspecialchars($page_title_full); ?></title>
    <meta name="title" content="<?php echo htmlspecialchars($page_title_full); ?>">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords"
        content="fatih psikolog, istanbul psikolog, uzman klinik psikolog, çocuk psikoloğu fatih, oyun terapisi istanbul, erguvan psikoloji, sena ceren parmaksız, sedat parmaksız, psikolojik danışmanlık fatih">
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
        "https://www.instagram.com/erguvanpsikoloji"
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
    <meta property="og:image:alt" content="Erguvan Psikoloji - Fatih Uzman Psikolog">
    <meta property="og:site_name" content="<?php echo $site_name; ?>">
    <meta property="og:locale" content="tr_TR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title_full); ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta name="twitter:site" content="@erguvanpsikoloji">

    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#8B5CF6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo $site_name; ?>">

    <!-- Favicon & Touch Icon -->
    <?php
    $logoWebpPathFavicon = __DIR__ . '/../assets/images/logo.webp';
    $logoPngPathFavicon = __DIR__ . '/../assets/images/logo.png';
    $faviconUrl = file_exists($logoWebpPathFavicon) ? asset_url('images/logo.webp') : (file_exists($logoPngPathFavicon) ? asset_url('images/logo.png') : '');
    if ($faviconUrl):
        ?>
        <link rel="icon" type="image/<?php echo strpos($faviconUrl, '.webp') !== false ? 'webp' : 'png'; ?>"
            href="<?php echo $faviconUrl; ?>" sizes="32x32">
        <link rel="apple-touch-icon" href="<?php echo $faviconUrl; ?>" sizes="180x180">
        <link rel="preload" href="<?php echo $faviconUrl; ?>" as="image" fetchpriority="low">
    <?php endif; ?>

    <!-- Resource Hints & Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">

    <!-- Preload Critical Fonts for FCP -->
    <link rel="preload"
        href="https://fonts.gstatic.com/s/inter/v13/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKGGKmAzCXBTclR7S.woff2" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload"
        href="https://fonts.gstatic.com/s/playfairdisplay/v37/6nuEX8F1nYSt9YYWHWIlf03S7AP8PShAL6vO.woff2" as="font"
        type="font/woff2" crossorigin>

    <!-- Google Services (Lazy Load for PageSpeed 90+) -->
    <script>
        (function () {
            var gaId = '<?php echo htmlspecialchars($google_settings['google_analytics_id'] ?? ''); ?>';
            var gtmId = '<?php echo htmlspecialchars($google_settings['google_tag_manager'] ?? ''); ?>';
            var adsId = '<?php echo htmlspecialchars($google_settings['google_ads_id'] ?? ''); ?>';
            var loaded = false;
            function loadServices() {
                if (loaded) return; loaded = true;
                if (gaId) {
                    var s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + gaId; document.head.appendChild(s);
                    window.dataLayer = window.dataLayer || []; function gtag() { dataLayer.push(arguments); } window.gtag = gtag; gtag('js', new Date()); gtag('config', gaId);
                }
                if (gtmId) {
                    (function (w, d, s, l, i) {
                        w[l] = w[l] || []; w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
                        var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
                    })(window, document, 'script', 'dataLayer', gtmId);
                }
                if (adsId && adsId !== gaId) {
                    var s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + adsId; document.head.appendChild(s);
                    window.dataLayer = window.dataLayer || []; function gtag() { dataLayer.push(arguments); } gtag('config', adsId);
                }
            }
            var events = ['mousedown', 'keydown', 'touchstart'];
            events.forEach(function (e) { window.addEventListener(e, loadServices, { once: true, passive: true }); });
            setTimeout(loadServices, 6000);
        })();
    </script>

    <!-- Preload LCP Image with High Priority -->
    <?php if (isset($preload_image) && !empty($preload_image)): ?>
        <link rel="preload" href="<?php echo htmlspecialchars($preload_image); ?>" as="image" fetchpriority="high">
    <?php endif; ?>

    <!-- Critical CSS inline - FCP Optimized -->
    <style>
        /* Font-face declarations for instant text rendering */
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400 700;
            font-display: swap;
            /* Requested optimization */
            src: local('Inter'), local('Inter-Regular'),
                url('https://fonts.gstatic.com/s/inter/v13/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKGGKmAzCXBTclR7S.woff2') format('woff2');
        }

        @font-face {
            font-family: 'Playfair Display';
            font-style: normal;
            font-weight: 400 700;
            font-display: swap;
            /* Requested optimization */
            src: local('Playfair Display'), local('PlayfairDisplay-Regular'),
                url('https://fonts.gstatic.com/s/playfairdisplay/v37/6nuEX8F1nYSt9YYWHWIlf03S7AP8PShAL6vO.woff2') format('woff2');
        }

        /* CSS Variables */
        :root {
            --primary: #ec4899;
            --primary-dark: #db2777;
            --primary-light: #f472b6;
            --secondary: #f9a8d4;
            --accent: #fce7f3;
            --text-dark: #1e293b;
            --text-medium: #475569;
            --text-light: #94a3b8;
            --transition: all 0.3s ease;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(236, 72, 153, 0.15);
        }

        /* Base styles */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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

        /* Header - Critical for FCP */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #fff;
            z-index: 1000;
            height: 80px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo-image {
            height: 85px;
            width: auto;
            max-width: 230px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-family: 'Playfair Display', Georgia, serif;
            background: linear-gradient(45deg, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 0;
            position: relative;
            transition: color 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary);
        }

        .nav-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            z-index: 1001;
        }

        .nav-toggle span {
            width: 24px;
            height: 2px;
            background: var(--text-dark);
            transition: var(--transition);
        }

        /* Hero Section - Critical for FCP/LCP */
        .hero {
            position: relative;
            min-height: 600px;
            /* SYNCED: Fixed height to match style.css */
            display: flex;
            align-items: center;
            padding-top: 80px;
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            overflow: hidden;
            box-sizing: border-box;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* SYNCED: Match style.css exactly */
            gap: 4rem;
            align-items: center;
            width: 100%;
        }

        /* Slider - Critical Sync */
        .hero-slider {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .hero-slide {
            display: none;
            width: 100%;
            /* Defaults for inactive slides */
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .hero-slide.active {
            position: relative;
            /* Takes up space in flow */
            display: block;
            opacity: 1;
            visibility: visible;
            z-index: 1;
        }

        .hero-text {
            z-index: 2;
        }

        .hero-title {
            font-size: clamp(2rem, 6vw, 3rem);
            font-family: 'Playfair Display', Georgia, serif;
            line-height: 1.2;
            margin: 0 0 1.5rem;
            color: #1e293b;
        }

        /* ... existing styles ... */

        /* Content visibility REMOVED to prevent calc errors */
        section:not(.hero),
        footer {
            /* content-visibility removed */
        }

        /* Mobile Responsive - Critical */
        @media (max-width: 768px) {
            .header {
                height: 70px;
            }

            .nav {
                height: 70px;
            }

            .logo-image {
                height: 50px;
                max-width: 180px;
            }

            .nav-toggle {
                display: flex;
            }

            .nav-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 300px;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding: 120px 2rem 2rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transition: right 0.3s ease;
                align-items: flex-start;
                z-index: 1100;
            }

            .nav-menu.active {
                right: 0;
            }

            .nav-link {
                padding: 1rem 0;
                font-size: 1.1rem;
                border-bottom: 1px solid #f1f5f9;
                width: 100%;
            }

            .hero {
                padding-top: 70px;
                /* Rigid Height for CLS */
                min-height: 85vh;
                padding-bottom: 3rem;
                display: flex;
                flex-direction: column;
            }

            .hero-content {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-description {
                font-size: 1rem;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-image-wrapper img {
                max-width: 100%;
            }
        }

        /* Prevent FOUC */
        .no-js .nav-menu {
            right: 0 !important;
        }
    </style>

    <!-- Main CSS (Deferred Loading) -->
    <?php
    $minCssPath = __DIR__ . '/../assets/css/style.min.css';
    $cssFile = file_exists($minCssPath) ? 'style.min.css' : 'style.css';
    ?>
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

            // Eğer sürüm değişmişse veya ilk kez yükleniyorsa
            if (storedVersion !== currentVersion) {
                // Tüm çerezleri temizle
                document.cookie.split(";").forEach(function (c) {
                    var eqPos = c.indexOf("=");
                    var name = eqPos > -1 ? c.substr(0, eqPos).trim() : c.trim();
                    // Tüm olası path ve domain kombinasyonlarını dene
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=" + window.location.hostname;
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=." + window.location.hostname;
                });

                // LocalStorage'ı da temizle (opsiyonel - sadece site versiyonunu sakla)
                var keysToKeep = ['site_version'];
                for (var i = localStorage.length - 1; i >= 0; i--) {
                    var key = localStorage.key(i);
                    if (key && keysToKeep.indexOf(key) === -1) {
                        localStorage.removeItem(key);
                    }
                }

                // Yeni sürümü kaydet
                localStorage.setItem('site_version', currentVersion);

                // Sayfayı yenile (cache temizleme için)
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
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="<?php echo url(); ?>" class="logo" aria-label="Erguvan Psikoloji Ana Sayfa">
                    <?php
                    $logoWebp = asset_url('images/logo.webp');
                    $logoPng = asset_url('images/logo.png');
                    $logoWebpPath = __DIR__ . '/../assets/images/logo.webp';
                    $logoPngPath = __DIR__ . '/../assets/images/logo.png';
                    ?>
                    <?php if (file_exists($logoWebpPath)): ?>
                        <img src="<?php echo $logoWebp; ?>" alt="Erguvan Psikoloji Logo" class="logo-image" width="230"
                            height="85"
                            style="height: 85px; width: auto; max-width: 230px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));"
                            loading="eager" fetchpriority="high" decoding="async">
                    <?php elseif (file_exists($logoPngPath)): ?>
                        <img src="<?php echo $logoPng; ?>" alt="Erguvan Psikoloji Logo" class="logo-image" width="230"
                            height="85"
                            style="height: 85px; width: auto; max-width: 230px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));"
                            loading="eager" fetchpriority="high" decoding="async">
                    <?php else: ?>
                        <!-- Fallback Logo if image missing -->
                        <div
                            style="height: 85px; width: 85px; background: linear-gradient(135deg, #ec4899, #f9a8d4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 32px; box-shadow: 0 4px 10px rgba(236,72,153,0.3);">
                            E</div>
                    <?php endif; ?>
                    <div style="display: flex; flex-direction: column;">
                        <span class="logo-text"
                            style="font-size: 1.5rem; font-family: 'Playfair Display', serif; background: linear-gradient(45deg, #1e293b, #475569); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Erguvan
                            Psikoloji</span>
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 500; letter-spacing: 0.5px;">Uzman
                            Klinik Psikolog Desteği</span>
                    </div>
                </a>
                <div class="nav-menu" id="navMenu">
                    <a href="<?php echo url(); ?>" class="nav-link <?php echo $page == 'home' ? 'active' : ''; ?>"
                        title="Erguvan Psikoloji Ana Sayfa">Ana Sayfa</a>
                    <a href="<?php echo url('#hizmetler'); ?>" class="nav-link"
                        title="Terapi Hizmetlerimiz">Hizmetlerimiz</a>
                    <a href="<?php echo url('#hakkimda'); ?>" class="nav-link"
                        title="Hakkımızda Bilgi Alın">Hakkımızda</a>
                    <a href="<?php echo url('#ekibimiz'); ?>" class="nav-link" title="Uzman Ekibimiz">Ekibimiz</a>
                    <a href="<?php echo page_url('blog.php'); ?>"
                        class="nav-link <?php echo $page == 'blog' ? 'active' : ''; ?>" title="Psikoloji Blogu">Blog</a>
                    <a href="<?php echo url('#iletisim'); ?>" class="nav-link" title="İletişim Bilgileri">İletişim</a>
                    <a href="<?php echo url('#randevu'); ?>" class="btn btn-primary btn-sm"
                        aria-label="Online Randevu Al">Randevu Al</a>
                </div>
                <button class="nav-toggle" id="navToggle" aria-label="Menüyü aç/kapat" aria-expanded="false"
                    aria-controls="navMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>
EOD;

if (file_put_contents('includes/header.php', $header_content)) {
    echo "<div style='color:green'>✅ includes/header.php başarıyla onarıldı.</div>";
} else {
    echo "<div style='color:red'>❌ includes/header.php yazılamadı. Dosya izinlerini kontrol edin.</div>";
}

// style.css ve style.min.css dosyasının varlığını kontrol et ve cache temizle
if (file_exists('assets/css/style.css')) {
    touch('assets/css/style.css');
    echo "<div style='color:green'>✅ style.css tarihi güncellendi.</div>";
}

if (file_exists('assets/css/style.min.css')) {
    touch('assets/css/style.min.css');
    echo "<div style='color:green'>✅ style.min.css tarihi güncellendi.</div>";
}

echo "<br><a href='/' style='background:#ec4899; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Ana Sayfaya Dön</a>";
?>