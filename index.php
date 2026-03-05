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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts Inlined - No render-blocking network requests (Zero CLS, Max LCP) -->
    <style id="critical-fonts">
        /* cyrillic-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 300;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WRhyzbi.woff2) format('woff2');
            unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
        }

        /* cyrillic */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 300;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459W1hyzbi.woff2) format('woff2');
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
        }

        /* vietnamese */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 300;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WZhyzbi.woff2) format('woff2');
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
        }

        /* latin-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 300;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
            unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        }

        /* latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 300;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }

        /* cyrillic-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WRhyzbi.woff2) format('woff2');
            unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
        }

        /* cyrillic */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459W1hyzbi.woff2) format('woff2');
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
        }

        /* vietnamese */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WZhyzbi.woff2) format('woff2');
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
        }

        /* latin-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
            unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        }

        /* latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }

        /* cyrillic-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WRhyzbi.woff2) format('woff2');
            unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
        }

        /* cyrillic */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459W1hyzbi.woff2) format('woff2');
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
        }

        /* vietnamese */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WZhyzbi.woff2) format('woff2');
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
        }

        /* latin-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
            unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        }

        /* latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }

        /* cyrillic-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WRhyzbi.woff2) format('woff2');
            unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
        }

        /* cyrillic */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459W1hyzbi.woff2) format('woff2');
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
        }

        /* vietnamese */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WZhyzbi.woff2) format('woff2');
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
        }

        /* latin-ext */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
            unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        }

        /* latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }

        /* cyrillic-ext */
        @font-face {
            font-family: 'Prata';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/prata/v22/6xKhdSpbNNCT-sWCCm7JLQ.woff2) format('woff2');
            unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
        }

        /* cyrillic */
        @font-face {
            font-family: 'Prata';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/prata/v22/6xKhdSpbNNCT-sWLCm7JLQ.woff2) format('woff2');
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
        }

        /* vietnamese */
        @font-face {
            font-family: 'Prata';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/prata/v22/6xKhdSpbNNCT-sWACm7JLQ.woff2) format('woff2');
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
        }

        /* latin */
        @font-face {
            font-family: 'Prata';
            font-style: normal;
            font-weight: 400;
            font-display: optional;
            src: url(https://fonts.gstatic.com/s/prata/v22/6xKhdSpbNNCT-sWPCm4.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
    </style>

    <!-- Google Fonts Preload - Faster FCP without CLS penalties -->
    <link rel="preload" as="font" href="https://fonts.gstatic.com/s/prata/v20/82bm8mi0QvL_AaB2.woff2" type="font/woff2"
        crossorigin>
    <link rel="preload" as="font" href="https://fonts.gstatic.com/s/montserrat/v25/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2"
        type="font/woff2" crossorigin>

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
            height: 90px !important;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(157, 23, 77, 0.05);
            /* Lighter border */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            /* Premium shadow */
            transition: var(--transition);
            padding: 0;
            /* Remove default padding to allow container to handle centering */
            display: flex;
            align-items: center;
        }

        .navbar.scrolled {
            height: 90px !important;
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
            width: 57px !important;
            /* Fixed width based on 124/130 ratio */
            object-fit: contain;
            mix-blend-mode: multiply;
            aspect-ratio: 124 / 130;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            margin-left: 1.2rem;
            line-height: 1;
            min-width: 200px;
            /* Pre-reserve width for Title/Subtitle */
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
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            flex-shrink: 0;
            margin-right: 0;
            /* Managed by justify-content */
        }

        .nav-links {
            display: flex;
            gap: 32px;
            /* Increased gap */
            list-style: none;
            align-items: center;
            flex-shrink: 1;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 500;
            /* Modern lighter weight */
            font-size: 16px;
            /* Slightly refined size */
            padding: 8px 4px;
            /* Vertical padding only, gap handles horizontal */
            white-space: nowrap;
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

        .mobile-only {
            display: none !important;
        }

        .nav-phone {
            display: none !important;
        }

        .nav-links a:hover::after {
            width: 100%;
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

        /* Media Queries - Critical */
        @media (max-width: 900px) {
            .navbar {
                padding: 1rem 1.5rem !important;
                height: 80px !important;
                min-height: 80px !important;
                /* Strictly fixed height for navbar on mobile to avoid CLS */
            }

            .navbar .logo img {
                height: 50px !important;
                max-height: 50px !important;
                width: 48px !important;
                /* Fixed width for 124/130 ratio to avoid CLS */
                object-fit: contain;
            }

            .logo-title {
                font-size: 1.3rem !important;
            }

            .logo-text {
                min-width: 180px !important;
                height: auto;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                margin-left: 0.8rem !important;
            }

            .hero {
                padding-top: 100px !important;
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

            /* Floating Contact - Classic Round Buttons */
            .floating-contact {
                position: fixed !important;
                bottom: 25px !important;
                right: 25px !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                z-index: 9999 !important;
                width: 50px !important;
                height: 112px !important;
            }

            .fc-classic {
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: white !important;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
                text-decoration: none !important;
            }

            .fc-classic.wa {
                background: #25D366 !important;
            }

            .fc-classic.ph {
                background: #1D2D50 !important;
            }

            .fc-classic:hover {
                transform: scale(1.1);
            }
        }
    </style>
</head>

<body>
    <!-- Content Started (JS deferred at bottom) -->
    <!-- Navigation -->
    <div class="nav-container">
        <a href="#" class="logo"><img src="<?php echo webp_url('assets/images/logo_icon.png'); ?>"
                alt="Erguvan Psikoloji Logo" width="124" height="130">
            <div class="logo-text"><span class="logo-title">Erguvan Psikoloji</span><span class="logo-subtitle">Uzman
                    Klinik Psikolog Desteği</span></div>
        </a>
        <div class="menu-toggle" id="navToggle"><span></span><span></span><span></span></div>
        <ul class="nav-links" id="navMenu">
            <li><a href="#home" class="nav-link">Ana Sayfa</a></li>
            <li><a href="#team" class="nav-link">Ekibimiz</a></li>
            <li><a href="pages/ofisimiz.php" class="nav-link">Ofisimiz</a></li>
            <li><a href="#hizmetler" class="nav-link">Hizmetler</a></li>
            <li><a href="#blog" class="nav-link">Blog</a></li>
            <li><a href="#contact" class="nav-link">İletişim</a></li>
            <li class="nav-phone"><a href="tel:+905511765285" class="nav-link"><svg class="svg-icon"
                        viewBox="0 0 512 512"
                        style="width:16px; height:16px; margin-right:8px; vertical-align: middle; fill:currentColor;">
                        <path
                            d="M497.39 361.8l-112-48a24 24 0 0 0-29.45 6.7L315.6 371.9a340.5 340.5 0 0 1-197.6-197.6L182.2 131.7a24 24 0 0 0 6.7-29.45l-48-112A24 24 0 0 0 113.2 0L64 96a344.2 344.2 0 0 0 484 484l96-49.2a24 24 0 0 0-14.61-42.8z" />
                    </svg>0551
                    176 52 85 </a></li>
            <li class="mobile-only"><a href="tel:+905511765285" class="btn-premium"
                    style="padding: 1rem 2rem; width: 100%; text-align: center;">Hemen Ara</a></li>
        </ul>
        <a href="tel:+905511765285" class="btn-premium head-cta"
            style="padding: 0.8rem 1.5rem; font-size: 0.9rem; margin-left: 20px;">Hemen Ara</a>
    </div>
    </div>
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
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg></div>
                    <h3>Bireysel Terapi</h3>
                    <p>Kendinizi anlama ve hayat kalitenizi artırma yolculuğunda yanınızdayız.</p>
                    <div class="service-focus-label">Odak Alanları:</div>
                    <div class="service-focus-areas">Depresyon,
                        Kaygı Bozuklukları,
                        Travma</div>
                </div>
                <!-- Aile ve Çift Terapisi -->
                <div class="service-card">
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg></div>
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
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        </svg></div>
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
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                            </rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg></div>
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
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z">
                            </path>
                        </svg></div>
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
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z">
                            </path>
                        </svg></div>
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
                    <div class="service-icon-wrapper"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.5 20a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z">
                            </path>
                            <path d="M14.5 20a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z">
                            </path>
                            <path d="M12 15V7"></path>
                            <path d="M12 7a5 5 0 1 1 10 0 5 5 0 1 1-10 0z">
                            </path>
                            <path d="M12 7a5 5 0 1 0-10 0 5 5 0 1 0 10 0z">
                            </path>
                        </svg></div>
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
                        bana sağladıkları profesyonel destek ve akademik yaklaşım,
                        hayata bakış açımı tamamen değiştirdi. Güven veren bir ortamda olduğumu her
                        an hissettim."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>H. A.</h4><span>Bireysel Terapi Danışanı</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card"><i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">"Eşimle yaşadığımız iletişim sorunlarını çözmemizde,
                        uzman kadronun objektif ve yapıcı yaklaşımı çok etkili oldu. İlişkimizde
                        yeni ve sağlıklı bir temel kurmamıza yardımcı oldular."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>M. & E. Ş.</h4><span>Aile & Çift Terapisi Danışanı</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card"><i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">"Kızımızın gelişim sürecinde karşılaştığımız
                        zorluklarda,
                        ekibin çocuk dünyasına olan derin anlayışı ve ebeveyn olarak bize sundukları
                        rehberlik paha biçilemezdi. Çok teşekkür ederiz."
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
                                </p><a href="<?php echo url('blog/' . $post['slug']); ?>" class="btn-premium"
                                    style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">Devamı
                                    nı
                                    Oku</a>
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