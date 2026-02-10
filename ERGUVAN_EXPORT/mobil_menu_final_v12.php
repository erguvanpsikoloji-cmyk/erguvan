<?php
/**
 * ERGUVAN PSİKOLOJİ - MOBİL MENÜ FİNAL DÜZELTME (V12)
 * Bu betik, 0 byte olan v11 sürümü yerine geçer.
 * Özellikler: Tam paralel hizalama, Backdrop Blur, Body Scroll Lock, Smooth Transitions.
 */

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

$target_file = 'includes/header.php';
$backup_file = 'includes/header_backup_v12.php';

if (!file_exists($target_file)) {
    die("HATA: $target_file bulunamadı!");
}

// Yedek al
copy($target_file, $backup_file);

$content = file_get_contents($target_file);

// 1. CSS GÜNCELLEMESİ (Backdrop Blur ve Premium Görünüm)
$premium_css = '
            .nav-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 100%;
                height: 100vh;
                flex-direction: column;
                justify-content: flex-start;
                padding: 100px 1.5rem 2rem;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                gap: 0;
                z-index: 1100;
                overflow-y: auto;
            }

            .nav-menu.active {
                right: 0;
            }

            .nav-link {
                padding: 1.25rem 0;
                font-size: 1.15rem;
                font-weight: 600;
                border-bottom: 1px solid rgba(0,0,0,0.05);
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: var(--text-dark);
            }

            .nav-link::after {
                content: "→";
                opacity: 0.3;
                font-size: 1.2rem;
            }

            .nav-link.active {
                color: var(--primary);
            }

            /* Burger Menü Animasyonu */
            .nav-toggle.active span:nth-child(1) {
                transform: translateY(7px) rotate(45deg);
            }
            .nav-toggle.active span:nth-child(2) {
                opacity: 0;
            }
            .nav-toggle.active span:nth-child(3) {
                transform: translateY(-7px) rotate(-45deg);
            }

            /* Menü açıkken sayfa kaymasını engelle */
            body.menu-open {
                overflow: hidden !important;
            }';

// Önceki mobil menü CSS bloğunu bul ve değiştir
$pattern_css = '/@media \(max-width: 768px\) \{.*?\.nav-menu \{.*?width: (100%|300px);.*?gap: 0;.*?\}\s+\.nav-menu\.active \{.*?right: 0;.*?\}\s+\.nav-link \{.*?\}\s+\}/s';
if (preg_match($pattern_css, $content)) {
    $content = preg_replace($pattern_css, '@media (max-width: 768px) {' . $premium_css . '        }', $content);
} else {
    // Eğer desen tam eşleşmezse, head sonuna ekle
    $content = str_replace('</style>', $premium_css . "\n        </style>", $content);
}

// 2. JAVASCRIPT GÜNCELLEMESİ (Body Scroll Lock ve Smart Toggle)
$premium_js = '
    <script id="mobile-menu-fix-v12">
    document.addEventListener("DOMContentLoaded", function() {
        const toggle = document.getElementById("navToggle");
        const menu = document.getElementById("navMenu");
        const body = document.body;

        if (toggle && menu) {
            function toggleMenu(forceClose = false) {
                const isOpen = forceClose ? false : menu.classList.toggle("active");
                if (forceClose) menu.classList.remove("active");
                
                toggle.classList.toggle("active", isOpen);
                body.classList.toggle("menu-open", isOpen);
                toggle.setAttribute("aria-expanded", isOpen);
            }

            toggle.addEventListener("click", function(e) {
                e.stopPropagation();
                toggleMenu();
            });

            // Linklere tıklandığında menüyü kapat
            document.querySelectorAll(".nav-link, .nav-menu .btn").forEach(link => {
                link.addEventListener("click", () => {
                    if (menu.classList.contains("active")) {
                        setTimeout(() => toggleMenu(true), 300);
                    }
                });
            });

            // Menü dışına tıklandığında kapat
            document.addEventListener("click", (e) => {
                if (menu.classList.contains("active") && !menu.contains(e.target) && !toggle.contains(e.target)) {
                    toggleMenu(true);
                }
            });
        }
    });
    </script>';

// Eski scriptleri ve footer'ı kontrol et
if (strpos($content, '</body>') !== false) {
    if (strpos($content, 'mobile-menu-fix') !== false) {
        $content = preg_replace('/<script id="mobile-menu-fix.*?<\/script>/s', $premium_js, $content);
    } else {
        $content = str_replace('</body>', $premium_js . "\n</body>", $content);
    }
}

// Yazdır
if (file_put_contents($target_file, $content)) {
    echo "<div style='font-family:sans-serif; padding:20px; border-radius:10px; background:#efe; color:#151; border:1px solid #cfc;'>
        <h2>✅ Mobil Menü v12 Başarıyla Kuruldu!</h2>
        <p><strong>Değişiklikler:</strong></p>
        <ul>
            <li>0b olan v11 sürümü geçersiz kılındı.</li>
            <li><strong>Backdrop Blur</strong> eklendi (Menü arkası buzlu cam efekti).</li>
            <li><strong>Body Scroll Lock</strong> aktif edildi (Menü açıkken arka plan kaymaz).</li>
            <li><strong>Akıllı Kapanma:</strong> Linklere tıklandığında artık menü otomatik kapanıyor.</li>
            <li><strong>Burger Animasyonu:</strong> Menü ikonu artık şık bir 'X' işaretine dönüşüyor.</li>
        </ul>
        <p>Lütfen sayfayı <strong>Ctrl + F5</strong> ile yenileyerek kontrol edin.</p>
        <hr>
        <p><small>Yedek: $backup_file adresine alındı.</small></p>
    </div>";
} else {
    echo "HATA: Dosya yazılamadı!";
}
?>