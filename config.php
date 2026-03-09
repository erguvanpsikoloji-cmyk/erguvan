<?php
/**
 * Site Konfigürasyon Dosyası
 * URL yapısı ve temel ayarlar
 */

// Base URL Ayarları
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Otomatik Base URL Algılama (Hem Localhost hem Canlı Sunucu hem de Alt Klasör uyumlu)
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dir = str_replace('\\', '/', __DIR__);

// Document root ile uyuşmazlık (symlink vb.) durumunda güvenli fallback
if (strpos($dir, $doc_root) !== 0) {
    // Riskli durum, boş varsayalım
    define('BASE_URL', '');
} else {
    $base_url = str_replace($doc_root, '', $dir);
    $base_url = '/' . trim($base_url, '/');
    if ($base_url === '/') {
        $base_url = '';
    }
    define('BASE_URL', $base_url);
}

// Versiyon - Cache temizleme ve asset güncellemeleri için
define('VERSION', 'v76');

// Google Maps API Key
define('GOOGLE_MAPS_API_KEY', 'AIzaSyA_BZgHTkpStO80rI3Ksbd9Dj_bF7P1CxE');

// Site URL'leri
define('SITE_URL', BASE_URL);
define('ADMIN_URL', BASE_URL . '/admin');
define('PAGES_URL', BASE_URL . '/pages');
define('ADMIN_PAGES_URL', BASE_URL . '/admin/pages');

// Asset URL'leri
define('ASSETS_URL', BASE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');
define('ADMIN_ASSETS_URL', BASE_URL . '/admin/assets');

/**
 * URL helper fonksiyonları
 */

/**
 * Base URL ile birleştirilmiş URL döndürür
 * @param string $path URL yolu
 * @return string Tam URL
 */
function url($path = '')
{
    $path = ltrim($path, '/');
    if (empty($path)) {
        return empty(BASE_URL) ? '/' : BASE_URL;
    }
    return BASE_URL . '/' . $path;
}

/**
 * Admin URL'i döndürür
 * @param string $path Admin alt yolu
 * @return string Tam admin URL
 */
function admin_url($path = '')
{
    $path = ltrim($path, '/');
    if (empty($path)) {
        return ADMIN_URL;
    }
    return ADMIN_URL . '/' . $path;
}

/**
 * Sayfa URL'i döndürür
 * @param string $path Sayfa yolu
 * @return string Tam sayfa URL
 */
function page_url($path = '')
{
    $path = ltrim($path, '/');
    if (empty($path)) {
        return PAGES_URL;
    }
    return PAGES_URL . '/' . $path;
}

/**
 * Asset URL'i döndürür (Sürüm bazlı)
 * @param string $path Asset yolu
 * @return string Tam asset URL
 */
/**
 * Asset URL'i döndürür (Sürüm bazlı)
 * @param string $path Asset yolu
 * @return string Tam asset URL
 */
function asset_url($path = '')
{
    $path = ltrim($path, '/');
    $path = str_replace('\\', '/', $path);
    if (empty($path)) {
        return ASSETS_URL . '?v=' . VERSION;
    }
    return ASSETS_URL . '/' . $path . '?v=' . VERSION;
}

/**
 * CSS URL'i döndürür (Sürüm bazlı)
 * @param string $file CSS dosya adı
 * @return string Tam CSS URL
 */
function css_url($file = '')
{
    if (empty($file)) {
        return CSS_URL . '?v=' . VERSION;
    }
    $file = str_replace('\\', '/', $file);
    return CSS_URL . '/' . ltrim($file, '/') . '?v=' . VERSION;
}

/**
 * JS URL'i döndürür (Sürüm bazlı)
 * @param string $file JS dosya adı
 * @return string Tam JS URL
 */
function js_url($file = '')
{
    if (empty($file)) {
        return JS_URL . '?v=' . VERSION;
    }
    $file = str_replace('\\', '/', $file);
    return JS_URL . '/' . ltrim($file, '/') . '?v=' . VERSION;
}

/**
 * Görselin WebP versiyonu varsa döndürür, yoksa orijinalini döndürür.
 * @param string $path Görsel yolu (assets/images/...)
 * @param string $variant 'mobile' veya boş (varsayılan)
 * @return string Optimize edilmiş veya orijinal URL
 */
function webp_url($path, $variant = '')
{
    if (empty($path))
        return $path;
    if (strpos($path, 'http') === 0)
        return $path;

    static $webp_cache = [];
    $cacheKey = $path . '_' . $variant;
    if (isset($webp_cache[$cacheKey]))
        return $webp_cache[$cacheKey];

    // Clean path
    $cleanPath = ltrim($path, '/\\');
    $parts = explode('?', $cleanPath);
    $pathWithoutQuery = $parts[0];
    $extension = pathinfo($pathWithoutQuery, PATHINFO_EXTENSION);
    $basePath = substr($pathWithoutQuery, 0, -(strlen($extension) + 1));
    $v = (defined('VERSION') ? VERSION : '1');

    // Determing WebP Path
    $webpRelative = ($variant === 'mobile') ? ($basePath . '_mobile.webp') : ($basePath . '.webp');
    if ($variant === 'mobile' && strpos($basePath, '-mobile') === false && strpos($basePath, '_mobile') === false) {
        // Special case for mobile variant handling if path doesn't already have mobile suffix
        $webpRelative = $basePath . '-mobile.webp';
    }

    // Check availability
    $checkPaths = [
        __DIR__ . '/' . $webpRelative,
        __DIR__ . '/assets/' . $webpRelative,
        __DIR__ . '/' . $pathWithoutQuery,
        __DIR__ . '/assets/' . $pathWithoutQuery
    ];

    foreach ($checkPaths as $p) {
        if (file_exists($p) && filesize($p) > 0) {
            // Find the relative path from the checkPath
            $finalPath = (strpos($p, '/assets/') !== false) ? ('assets/' . ltrim(str_replace(__DIR__ . '/assets/', '', $p), '/')) : ltrim(str_replace(__DIR__, '', $p), '/');
            $res = url($finalPath) . '?v=' . $v;
            $webp_cache[$cacheKey] = $res;
            return $res;
        }
    }

    $res = url($cleanPath) . '?v=' . $v;
    $webp_cache[$cacheKey] = $res;
    return $res;
}

/**
 * Admin asset URL'i döndürür
 * @param string $file Asset dosya adı
 * @return string Tam admin asset URL
 */
function admin_asset_url($file = '')
{
    if (empty($file)) {
        return ADMIN_ASSETS_URL;
    }
    $file = str_replace('\\', '/', $file);
    return ADMIN_ASSETS_URL . '/' . ltrim($file, '/');
}

/**
 * Yönlendirme yapar
 * @param string $path Yönlendirilecek yol (tam URL veya göreli yol)
 * @param int $code HTTP status code
 */
function redirect($path, $code = 302)
{
    // Eğer zaten tam URL ise (http/https ile başlıyorsa) direkt kullan
    if (strpos($path, 'http') === 0) {
        $url = $path;
    }
    // Eğer zaten BASE_URL ile başlıyorsa (tam URL zaten oluşturulmuşsa) direkt kullan
    elseif (strpos($path, BASE_URL) === 0) {
        $url = $path;
    }
    // Değilse url() fonksiyonu ile oluştur
    else {
        $url = url($path);
    }
    header('Location: ' . $url, true, $code);
    exit;
}

