<?php
/**
 * ERGUVAN SPEED OPTIMIZER
 * 1. Updates .htaccess with Gzip & Caching.
 * 2. Adds loading="lazy" to images in index.php.
 * 3. Adds defer to scripts in index.php.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Erguvan Hız Optimizasyonu Başladı</h2>";

// --- 1. .htaccess OPTIMIZATION ---
$htaccessPath = __DIR__ . '/.htaccess';
$htaccessRules = <<<EOD

# --- SPEED OPTIMIZATION START ---
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript application/json
</IfModule>

<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 month"
  ExpiresByType image/x-icon "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType text/javascript "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
</IfModule>
# --- SPEED OPTIMIZATION END ---
EOD;

if (file_exists($htaccessPath)) {
    $content = file_get_contents($htaccessPath);
    if (strpos($content, 'SPEED OPTIMIZATION') === false) {
        file_put_contents($htaccessPath, $content . "\n" . $htaccessRules);
        echo "✅ .htaccess güncellendi (Gzip & Cache eklendi).<br>";
    } else {
        echo "ℹ️ .htaccess zaten optimize edilmiş.<br>";
    }
} else {
    file_put_contents($htaccessPath, "RewriteEngine On\n" . $htaccessRules);
    echo "✅ .htaccess oluşturuldu ve optimize edildi.<br>";
}

// --- 2. index.php OPTIMIZATION ---
$indexPath = __DIR__ . '/index.php';
if (file_exists($indexPath)) {
    $content = file_get_contents($indexPath);

    // Lazy Loading for Images
    $content = preg_replace('/<img(?![^>]*\bloading\s*=)([^>]+)>/i', '<img loading="lazy" $1>', $content);

    // Defer for Scripts
    $content = preg_replace('/<script(?![^>]*\bdefer\b)([^>]*\bsrc\s*=[^>]+)>/i', '<script defer $1>', $content);

    file_put_contents($indexPath, $content);
    echo "✅ index.php optimize edildi (Lazy loading & Defer JS).<br>";
}

echo "<h3>Optimizasyon Tamamlandı!</h3>";
?>