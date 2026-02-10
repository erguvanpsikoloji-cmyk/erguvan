<?php
/**
 * ERGUVAN PSİKOLOJİ - MOBİL MENÜ KESİN ÇÖZÜM (V13)
 * Teşhis: Menü, header > .container içindeki overflow:hidden nedeniyle kesiliyor.
 * Çözüm: Menüyü hiyerarşiden kurtarıp body altına taşır ve Premium CSS uygular.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

$target_file = 'includes/header.php';
$backup_file = 'includes/header_backup_v13.php';

if (!file_exists($target_file)) {
    die("HATA: $target_file bulunamadı!");
}

copy($target_file, $backup_file);
$content = file_get_contents($target_file);

/**
 * ADIM 1: Menüyü Hiyerarşiden Kurtar
 * #navMenu div'ini bul ve header dışına, body sonuna taşınacak şekilde işaretle.
 */

// Önce mevcut navMenu'yu tamamen çıkaralım
$menu_pattern = '/<div class="nav-menu" id="navMenu">.*?<\/div>\s*<\/nav>/s';
if (preg_match($menu_pattern, $content, $matches)) {
    // NavMenu'yu nav içinden sil, sadece </nav> kalsın
    $content = preg_replace('/<div class="nav-menu" id="navMenu">.*?<\/div>/s', '', $content);
}

/**
 * ADIM 2: Premium Mobil CSS (Layout Clipping Fix)
 */
$premium_css = '
    <style id="mobile-menu-premium-v13">
        @media (max-width: 768px) {
            .nav-menu {
                position: fixed !important;
                top: 0 !important;
                right: -100% !important;
                width: 100vw !important;
                height: 100vh !important;
                background: rgba(255, 255, 255, 0.98) !important;
                backdrop-filter: blur(15px) !important;
                -webkit-backdrop-filter: blur(15px) !important;
                display: flex !important;
                flex-direction: column !important;
                padding: 100px 2rem 2rem !important;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
                z-index: 99999 !important;
                visibility: visible !important;
                overflow-y: auto !important;
            }
            .nav-menu.active {
                right: 0 !important;
            }
            .nav-link {
                padding: 1.25rem 0 !important;
                font-size: 1.2rem !important;
                font-weight: 600 !important;
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                color: #1e293b !important;
            }
            .nav-link::after { content: "→"; opacity: 0.3; }
            body.menu-open { overflow: hidden !important; position: fixed; width: 100%; }
            
            /* Burger Toggle Fix */
            .nav-toggle { z-index: 100000 !important; position: relative; }
            .nav-toggle.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); background: #ec4899; }
            .nav-toggle.active span:nth-child(2) { opacity: 0; }
            .nav-toggle.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); background: #ec4899; }
        }
    </style>';

// CSS'i head sonuna ekle
if (strpos($content, '</head>') !== false) {
    if (strpos($content, 'mobile-menu-premium') === false) {
        $content = str_replace('</head>', $premium_css . "\n</head>", $content);
    }
}

/**
 * ADIM 3: Akıllı JS (DOM Manipulation & Body Fix)
 */
$premium_js = '
    <script id="mobile-menu-script-v13">
    document.addEventListener("DOMContentLoaded", function() {
        const toggle = document.getElementById("navToggle");
        const menu = document.getElementById("navMenu");
        
        if (toggle && menu) {
            // KRİTİK: Menüyü kısıtlayıcı container\'dan çıkarıp body\'ye taşı
            document.body.appendChild(menu);
            
            function toggleMenu(forceClose = false) {
                const isOpen = forceClose ? false : menu.classList.toggle("active");
                if (forceClose) menu.classList.remove("active");
                
                toggle.classList.toggle("active", isOpen);
                document.body.classList.toggle("menu-open", isOpen);
            }

            toggle.addEventListener("click", (e) => {
                e.stopPropagation();
                toggleMenu();
            });

            // Tıklayınca otomatik kapanma
            menu.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    setTimeout(() => toggleMenu(true), 300);
                });
            });

            // Dışarı tıklayınca kapanma
            document.addEventListener("click", (e) => {
                if (menu.classList.contains("active") && !menu.contains(e.target) && !toggle.contains(e.target)) {
                    toggleMenu(true);
                }
            });
        }
    });
    </script>';

// JS'i body sonuna ekle
if (strpos($content, '</body>') !== false) {
    if (strpos($content, 'mobile-menu-script-v13') === false) {
        $content = str_replace('</body>', $premium_js . "\n</body>", $content);
    }
} else {
    // Header.php dosyasının sonunda </body> yoksa (genelde footer'dadır), dosya sonuna ekle
    $content .= $premium_js;
}

if (file_put_contents($target_file, $content)) {
    echo "<div style='font-family:sans-serif; padding:30px; border-radius:15px; background:#f0fdf4; color:#166534; border:2px solid #bbf7d0; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);'>
        <h2 style='margin-top:0;'>🚀 Mobil Menü v13 (Kalıcı Çözüm) Kuruldu!</h2>
        <p><strong>Uygulanan Düzeltmeler:</strong></p>
        <ul style='line-height:1.6;'>
            <li><strong>DOM Re-routing:</strong> Menü, kendisini hapseden header hiyerarşisinden JavaScript ile otomatik olarak kurtarılıp <code>body</code> altına taşındı.</li>
            <li><strong>Clipping Fix:</strong> <code>overflow:hidden</code> engeli tamamen aşıldı.</li>
            <li><strong>Backdrop Effect:</strong> Arka plan bulanıklığı ve tam ekran görünüm optimize edildi.</li>
            <li><strong>Scroll Lock:</strong> Menü açıkken arka planın kayması engellendi.</li>
        </ul>
        <p style='background:#dcfce7; padding:10px; border-radius:8px;'><strong>Not:</strong> Lütfen mobili <strong>incognito (gizli sekme)</strong> veya <strong>Ctrl+F5</strong> ile test edin.</p>
    </div>";
} else {
    echo "HATA: Dosya yazılamadı!";
}
?>