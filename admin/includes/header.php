<?php
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
requireLogin();

// Config dosyasını yükle
require_once __DIR__ . '/../../config.php';
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Panel - Erguvan Psikoloji</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(admin_asset_url('admin.css')); ?>">
    <?php if (isset($page) && in_array($page, ['blog', 'about', 'services'])): ?>
        <!-- Quill.js Rich Text Editor -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <?php endif; ?>
</head>

<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <div class="admin-logo-icon">
                    <?php
                    $adminIcon = admin_asset_url('admin-icon.png');
                    $adminIconPath = __DIR__ . '/../assets/admin-icon.png';
                    ?>
                    <?php if (file_exists($adminIconPath)): ?>
                        <img src="<?php echo $adminIcon; ?>" alt="Admin" class="admin-logo-image"
                            style="height: 40px; width: auto;">
                    <?php else: ?>
                        <svg width="40" height="40" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#ec4899;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#db2777;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path
                                d="M100,180 C80,180 70,170 70,150 L70,100 C70,80 80,70 100,70 C120,70 130,80 130,100 L130,150 C130,170 120,180 100,180 Z"
                                fill="#ffffff" fill-opacity="0.9" />
                            <circle cx="100" cy="60" r="25" fill="url(#logoGradient)" />
                            <path d="M90,50 Q85,40 80,35 M95,45 Q92,35 90,30 M105,45 Q108,35 110,30 M110,50 Q115,40 120,35"
                                stroke="url(#logoGradient)" stroke-width="3" fill="none" />
                        </svg>
                    <?php endif; ?>
                </div>
                <span class="logo-text">Erguvan Psikoloji</span>
            </div>
            <nav class="admin-nav">
                <a href="<?php echo admin_url(); ?>"
                    class="admin-nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="<?php echo admin_url('pages/about.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'about') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Hakkımızda
                </a>
                <a href="<?php echo admin_url('pages/settings.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'settings') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                        </path>
                    </svg>
                    Site Ayarları
                </a>
                <a href="<?php echo admin_url('pages/testimonials.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'testimonials') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Yorumlar
                </a>
                <a href="<?php echo admin_url('pages/faqs.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'faqs') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    SSS Yönetimi
                </a>
                <a href="<?php echo admin_url('pages/team.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'team') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Uzman Ekibimiz
                </a>
                <a href="<?php echo admin_url('pages/blog.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'blog') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Blog Yazıları
                </a>
                <a href="<?php echo admin_url('pages/sliders.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'sliders') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Sliderlar
                </a>
                <a href="<?php echo admin_url('pages/services.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'services') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Hizmetler
                </a>
                <a href="<?php echo admin_url('pages/appointments.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'appointments') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Randevu Talepleri
                </a>
                <a href="<?php echo admin_url('pages/seo.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'seo') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    SEO Yönetimi
                </a>
                <a href="<?php echo admin_url('pages/google-settings.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'google') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M21 12c0 1.66-1 3-2.5 3S16 13.66 16 12s1-3 2.5-3 2.5 1.34 2.5 3z"></path>
                        <path d="M5 6v12h14V6H5z"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Google Hizmetleri
                </a>
                <a href="<?php echo admin_url('pages/certificates.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'certificates') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    Sertifikalar
                </a>
                <a href="<?php echo admin_url('pages/office-images.php'); ?>"
                    class="admin-nav-link <?php echo ($page == 'office-images') ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Ofis Görselleri
                </a>
                <a href="<?php echo url(); ?>" target="_blank" class="admin-nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Siteyi Görüntüle
                </a>
                <a href="<?php echo admin_url('logout.php'); ?>" class="admin-nav-link logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Çıkış Yap
                </a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h1><?php echo $page_title ?? 'Admin Panel'; ?></h1>
                <div class="admin-user">
                    Hoş geldin, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </div>
            </div>
            <div class="admin-content">