<?php
/**
 * COMPLETE IMAGE FIX
 * Removes ALL lazy loading temporarily to diagnose the core issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    die("❌ index.php bulunamadı.");
}

$content = file_get_contents($indexPath);

// STEP 1: Remove ALL lazy loading attributes
$beforeCount = substr_count($content, 'loading="lazy"');
$content = str_replace('loading="lazy" ', '', $content);
$content = str_replace(' loading="lazy"', '', $content);
$afterCount = substr_count($content, 'loading="lazy"');

echo "<h3>Lazy Loading Temizleme</h3>";
echo "<p>Önce: $beforeCount adet lazy loading</p>";
echo "<p>Sonra: $afterCount adet lazy loading</p>";

// STEP 2: Ensure all image src attributes are present
$imgCount = preg_match_all('/<img[^>]*>/i', $content, $matches);
echo "<h3>Görsel Kontrolü</h3>";
echo "<p>Toplam img tag: $imgCount</p>";

// STEP 3: Check for broken src paths
if (preg_match_all('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', $content, $srcMatches)) {
    echo "<p>src bulunan: " . count($srcMatches[1]) . "</p>";
    echo "<ul>";
    foreach ($srcMatches[1] as $src) {
        echo "<li>" . htmlspecialchars($src) . "</li>";
    }
    echo "</ul>";
}

// STEP 4: Save changes
file_put_contents($indexPath, $content);

echo "<h3>✅ index.php güncellendi!</h3>";
echo "<p><strong>Tüm lazy loading kaldırıldı.</strong></p>";
echo "<p>Şimdi sayfayı yenileyip görsellerin görünüp görünmediğini kontrol edin.</p>";
?>