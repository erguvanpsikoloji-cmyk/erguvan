<?php
/**
 * ERGUVAN PSİKOLOJİ - MOBİL MENÜ FINAL ÇÖZÜM (V14)
 * Menü hapsolma sorununu JavaScript ile body altına taşıyarak tam ekran görünürlük sağlar.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

$f = 'includes/header.php';
if (!file_exists($f)) {
    die("Hata: header.php bulunamadı!");
}

copy($f, 'includes/header_backup_v14.php');
$c = file_get_contents($f);

// 1. CSS Ekleme (Bypass Layer Clipping)
$css = '
<style id="nav-v14-styles">
@media (max-width: 768px) {
    .header .container { overflow: visible !important; }
    #navMenu {
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
        transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        z-index: 99999 !important;
        overflow-y: auto !important;
    }
    #navMenu.active { right: 0 !important; }
    #navMenu .nav-link {
        display: flex !important;
        padding: 1.25rem 0 !important;
        font-size: 1.2rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        width: 100% !important;
    }
    .nav-toggle.active span:nth-child(1) { transform: translateY(7px) rotate(45deg) !important; background: #ec4899 !important; }
    .nav-toggle.active span:nth-child(2) { opacity: 0 !important; }
    .nav-toggle.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg) !important; background: #ec4899 !important; }
    body.menu-open { overflow: hidden !important; position: fixed; width: 100%; }
}
</style>';

if (strpos($c, 'nav-v14-styles') === false) {
    $c = str_replace('</head>', $css . "\n</head>", $c);
}

// 2. JS Ekleme (DOM Re-routing)
$js = '
<script id="nav-v14-script">
document.addEventListener("DOMContentLoaded", function() {
    var toggle = document.getElementById("navToggle");
    var menu = document.getElementById("navMenu");
    if (toggle && menu) {
        if (menu.parentElement !== document.body) {
            document.body.appendChild(menu);
        }
        toggle.addEventListener("click", function() {
            var isOpen = menu.classList.toggle("active");
            toggle.classList.toggle("active", isOpen);
            document.body.classList.toggle("menu-open", isOpen);
        });
        menu.querySelectorAll("a").forEach(function(link) {
            link.addEventListener("click", function() {
                menu.classList.remove("active");
                toggle.classList.remove("active");
                document.body.classList.remove("menu-open");
            });
        });
    }
});
</script>';

if (strpos($c, 'nav-v14-script') === false) {
    $c .= $js;
}

if (file_put_contents($f, $c)) {
    echo "<h1>✅ v14 Kurulumu Tamamlandı!</h1><p>Lütfen mobilde Ctrl+F5 ile test edin.</p>";
} else {
    echo "<h1>❌ Hata: Dosya güncellenemedi.</h1>";
}
?>