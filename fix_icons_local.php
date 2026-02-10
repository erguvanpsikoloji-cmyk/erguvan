<?php
/**
 * FIX SCRIPT: Floating Button Icons
 * Bu dosya index.php içindeki yüzer buton ikonlarını ve stillerini düzeltir.
 */

require_once __DIR__ . '/config.php';

$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    die("HATA: index.php bulunamadı.");
}

$content = file_get_contents($indexPath);

// 1. Mükerrer butonları ve eski sınıfları temizleyen yeni HTML yapısı
$newActionsHtml = '
    <div class="floating-actions">
        <a href="https://wa.me/905511765285" class="float-btn whatsapp" target="_blank" title="WhatsApp ile İletişime Geçin">
            <i class="fa-brands fa-whatsapp" style="color: white !important;"></i>
        </a>
        <a href="tel:+905511765285" class="float-btn phone" title="Bizi Arayın">
            <i class="fa-solid fa-phone" style="color: white !important;"></i>
        </a>
    </div>';

$newStyles = '
        .floating-actions {
            position: fixed;
            bottom: 25px;
            right: 25px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 9999;
        }
        .float-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .float-btn:hover { transform: scale(1.1); }
        .float-btn.whatsapp { background: #25D366; }
        .float-btn.phone { background: #0F172A; }
        .float-btn i { font-size: 30px !important; }';

// CSS'i enjekte et (Eski stillerin üzerine yazacak şekilde sona ekle)
if (strpos($content, '</style>') !== false) {
    $content = str_replace('</style>', $newStyles . "\n    </style>", $content);
}

// HTML butonlarını yerleştir (Mevcut floating-actions veya floating-container yapılarını temizle)
// Önce mevcut yapıları bulup temizleyelim
$content = preg_replace('/<div class="floating-actions">.*?<\/div>/s', '', $content);
$content = preg_replace('/<div class="floating-container">.*?<\/div>/s', '', $content);

// Yeni yapıyı </body> öncesine ekle
$content = str_replace('</body>', $newActionsHtml . "\n</body>", $content);

// Dosyayı kaydet
if (file_put_contents($indexPath, $content)) {
    echo "BAŞARILI: index.php güncellendi.";
} else {
    echo "HATA: Yazma izni yok.";
}
