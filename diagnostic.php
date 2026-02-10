<?php
/**
 * ERGUVAN DIAGNOSTIC TOOL
 * Kapsamlı hata tespiti ve raporlama
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Erguvan Hata Tespit Sistemi</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
h2 { color: #333; border-bottom: 2px solid #915F78; padding-bottom: 5px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";

// 1. INDEX.PHP KONTROLÜ
echo "<div class='section'>";
echo "<h2>1. index.php Dosya Kontrolü</h2>";
$indexPath = __DIR__ . '/index.php';
if (file_exists($indexPath)) {
    echo "<p class='success'>✅ index.php bulundu</p>";
    $content = file_get_contents($indexPath);

    // Görsel taglerini bul
    preg_match_all('/<img[^>]+>/i', $content, $imgTags);
    echo "<p>📊 Toplam görsel sayısı: <strong>" . count($imgTags[0]) . "</strong></p>";

    // İlk 3 görseli detaylı göster
    echo "<h3>İlk 3 Görsel Tag:</h3>";
    for ($i = 0; $i < min(3, count($imgTags[0])); $i++) {
        echo "<pre>" . htmlspecialchars($imgTags[0][$i]) . "</pre>";
    }

    // Lazy loading kontrolü
    $lazyCount = substr_count($content, 'loading="lazy"');
    echo "<p>🔄 Lazy loading kullanan görsel: <strong>$lazyCount</strong></p>";

} else {
    echo "<p class='error'>❌ index.php bulunamadı!</p>";
}
echo "</div>";

// 2. GÖRSEL DOSYALARI KONTROLÜ
echo "<div class='section'>";
echo "<h2>2. Görsel Dosyaları Fiziksel Kontrolü</h2>";

$imagesToCheck = [
    'assets/images/logo2026.png',
    'assets/images/team/sena.jpg',
    'assets/images/team/sedat.jpg',
    'assets/images/office/ofis-1.jpg',
    'assets/images/office/office2.jpg'
];

foreach ($imagesToCheck as $img) {
    $fullPath = __DIR__ . '/' . $img;
    if (file_exists($fullPath)) {
        $size = filesize($fullPath);
        $readable = is_readable($fullPath);
        echo "<p class='success'>✅ $img - " . round($size / 1024, 2) . " KB" .
            ($readable ? "" : " <span class='error'>(Okunamıyor!)</span>") . "</p>";
    } else {
        echo "<p class='error'>❌ $img - BULUNAMADI!</p>";
    }
}
echo "</div>";

// 3. ASSETS KLASÖR İZİNLERİ
echo "<div class='section'>";
echo "<h2>3. Klasör İzinleri Kontrolü</h2>";
$dirs = ['assets', 'assets/images', 'assets/images/team', 'assets/images/office'];
foreach ($dirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        $readable = is_readable($fullPath);
        echo "<p class='success'>✅ /$dir - İzinler: $perms" .
            ($readable ? "" : " <span class='error'>(Okunamıyor!)</span>") . "</p>";
    } else {
        echo "<p class='error'>❌ /$dir - Klasör bulunamadı!</p>";
    }
}
echo "</div>";

// 4. .HTACCESS KONTROLÜ
echo "<div class='section'>";
echo "<h2>4. .htaccess Kuralları</h2>";
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    $htaccess = file_get_contents($htaccessPath);
    echo "<p class='success'>✅ .htaccess bulundu</p>";

    // Gzip kontrolü
    if (strpos($htaccess, 'mod_deflate') !== false) {
        echo "<p class='success'>✅ Gzip sıkıştırma aktif</p>";
    } else {
        echo "<p class='warning'>⚠️ Gzip sıkıştırma bulunamadı</p>";
    }

    // Cache kontrolü
    if (strpos($htaccess, 'mod_expires') !== false) {
        echo "<p class='success'>✅ Browser caching aktif</p>";
    } else {
        echo "<p class='warning'>⚠️ Browser caching bulunamadı</p>";
    }

    // Görsel erişimini engelleyen kural var mı?
    if (preg_match('/RewriteRule.*\.(jpg|jpeg|png|gif|webp)/i', $htaccess)) {
        echo "<p class='error'>⚠️ UYARI: Görselleri etkileyen RewriteRule tespit edildi!</p>";
        echo "<pre>" . htmlspecialchars($htaccess) . "</pre>";
    }
} else {
    echo "<p class='warning'>⚠️ .htaccess bulunamadı</p>";
}
echo "</div>";

// 5. SUNUCU BİLGİLERİ
echo "<div class='section'>";
echo "<h2>5. Sunucu Bilgileri</h2>";
echo "<p>📍 Document Root: <code>" . $_SERVER['DOCUMENT_ROOT'] . "</code></p>";
echo "<p>📍 Script Path: <code>" . __DIR__ . "</code></p>";
echo "<p>🌐 Server Software: <code>" . $_SERVER['SERVER_SOFTWARE'] . "</code></p>";
echo "<p>🔧 PHP Versiyonu: <code>" . phpversion() . "</code></p>";
echo "</div>";

// 6. ÖNERİLER
echo "<div class='section'>";
echo "<h2>6. Öneriler ve Çözümler</h2>";
echo "<ul>";
echo "<li>Görseller sunucuda mevcut ancak tarayıcıda görünmüyorsa: <strong>Tarayıcı cache'ini temizleyin</strong></li>";
echo "<li>Logo lazy loading kullanıyorsa: <strong>Logo'dan lazy loading kaldırılmalı (LCP için kötü)</strong></li>";
echo "<li>Console'da hata varsa: <strong>F12 > Console sekmesini kontrol edin</strong></li>";
echo "<li>CSS ile gizlenmiş olabilir: <strong>F12 > Elements ile img etiketlerini inceleyin</strong></li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>🎯 Rapor Tarihi:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>