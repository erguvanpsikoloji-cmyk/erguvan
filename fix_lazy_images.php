<?php
/**
 * FIX LAZY LOADING - EXCLUDE CRITICAL IMAGES
 * Logo and hero images should NOT be lazy loaded (bad for LCP)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    die("Hata: index.php bulunamadı.");
}

$content = file_get_contents($indexPath);

// Remove lazy loading from logo
$content = preg_replace(
    '/<img loading="lazy"([^>]*class="[^"]*logo[^"]*"[^>]*)>/i',
    '<img $1>',
    $content
);

// Remove lazy loading from images in navbar
$content = preg_replace(
    '/<img loading="lazy"([^>]*(?:navbar|nav|logo)[^>]*)>/i',
    '<img $1>',
    $content
);

// Also check if src attributes are missing or broken
if (preg_match_all('/<img[^>]*\bsrc\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
    echo "Görsel sayısı: " . count($matches[0]) . "<br>";
    echo "İlk 3 görsel:<br>";
    for ($i = 0; $i < min(3, count($matches[1])); $i++) {
        echo "- " . htmlspecialchars($matches[1][$i]) . "<br>";
    }
}

file_put_contents($indexPath, $content);
echo "<h3>✅ Kritik görseller lazy loading'den muaf tutuldu!</h3>";
?>