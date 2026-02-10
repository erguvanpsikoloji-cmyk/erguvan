<?php
/**
 * ERGUVAN SPEED OPTIMIZER V2
 * Enhanced defer script implementation
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Defer Script İyileştirmesi</h2>";

$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    die("Hata: index.php bulunamadı.");
}

$content = file_get_contents($indexPath);

// More robust defer script implementation
// Add defer to external scripts that don't already have it
$content = preg_replace(
    '/<script\s+(?![^>]*\bdefer\b)([^>]*\bsrc\s*=\s*["\'][^"\']+["\'][^>]*)>/is',
    '<script defer $1>',
    $content
);

file_put_contents($indexPath, $content);
echo "✅ Defer attribute added to external scripts.<br>";

echo "<h3>İyileştirme Tamamlandı!</h3>";
?>