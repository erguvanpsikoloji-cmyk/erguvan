<?php
/**
 * ERGUVAN HEALER SCRIPT
 * 1. Cleans up garbage folders identified in screenshot.
 * 2. Fixes floating button icons in index.php.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Erguvan Operasyon Başladı</h2>";

// --- 1. CLEANUP ---
$garbage = [
    'temp_sena_check',
    'temp_sena_kurtarma',
    'temp_style.txt',
    'temp_update_text.php',
    'test_debug.php',
    'test_output.php',
    'ERGUVAN_EXPORT',
    '.local',
    'diagnostic_pkg',
    'erguvan_basit_kurulum',
    'erguvan_cikarilmis',
    'SON_COZUM',
    'YEDEK_2026_02_02',
    'admin/index_v38_fixed.php'
];

function deleteRecursive($dir)
{
    if (!file_exists($dir))
        return;
    if (is_file($dir)) {
        unlink($dir);
        return;
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        deleteRecursive("$dir/$file");
    }
    rmdir($dir);
}

foreach ($garbage as $item) {
    $path = __DIR__ . '/' . $item;
    if (file_exists($path)) {
        echo "Siliniyor: $item ... ";
        deleteRecursive($path);
        echo "BİTTİ.<br>";
    }
}

// --- 2. ICON FIX ---
$indexPath = __DIR__ . '/index.php';
if (file_exists($indexPath)) {
    $content = file_get_contents($indexPath);

    // Sadece henüz düzeltilmediyse müdahale et
    if (strpos($content, 'fa-brands fa-whatsapp') === false) {
        echo "İkonlar düzeltiliyor... ";

        $newActionsHtml = '
    <div class="floating-actions" style="position: fixed; bottom: 25px; right: 25px; display: flex; flex-direction: column; gap: 15px; z-index: 9999;">
        <a href="https://wa.me/905511765285" target="_blank" style="width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
            <i class="fa-brands fa-whatsapp" style="color: white !important; font-size: 30px !important;"></i>
        </a>
        <a href="tel:+905511765285" style="width: 60px; height: 60px; border-radius: 50%; background: #0F172A; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
            <i class="fa-solid fa-phone" style="color: white !important; font-size: 25px !important;"></i>
        </a>
    </div>';

        // Temizlik: Mevcut floating-actions veya floating-container yapılarını temizle
        $content = preg_replace('/<div class="floating-actions">.*?<\/div>/s', '', $content);
        $content = preg_replace('/<div class="floating-container">.*?<\/div>/s', '', $content);

        // Body sonuna ekle
        $content = str_replace('</body>', $newActionsHtml . "\n</body>", $content);

        if (file_put_contents($indexPath, $content)) {
            echo "BİTTİ.<br>";
        } else {
            echo "HATA: index.php üzerine yazılamadı.<br>";
        }
    } else {
        echo "İkonlar zaten güncel.<br>";
    }
}

echo "<h3>Operasyon Tamamlandı. Lütfen bu dosyayı (fix.php) silin.</h3>";
