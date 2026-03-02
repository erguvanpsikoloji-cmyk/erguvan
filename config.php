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
define('VERSION', 'v41');

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

    // Sorgu parametrelerini (query string) temizle, ama URL'de korumak için sakla
    $cleanPath = ltrim($path, '/\\');
    $parts = explode('?', $cleanPath);
    $pathWithoutQuery = $parts[0];
    $queryString = isset($parts[1]) ? '?' . $parts[1] : '';

    $extension = pathinfo($pathWithoutQuery, PATHINFO_EXTENSION);
    if (empty($extension))
        return url($cleanPath);

    $basePath = substr($pathWithoutQuery, 0, -(strlen($extension) + 1));
    if ($variant === 'mobile') {
        $webpPath = $basePath . '_mobile.webp';
    } else {
        $webpPath = $basePath . '.webp';
    }

    $v = (defined('VERSION') ? VERSION : '1');

    // Senaryo 1: Verilen yol doğrudan kök dizinde mi?
    $fullWebpPath = __DIR__ . '/' . $webpPath;
    if (file_exists($fullWebpPath) && filesize($fullWebpPath) > 0) {
        return url($webpPath) . '?v=' . $v;
    }

    // Senaryo 2: Verilen yol assets/ altında mı?
    if (strpos($pathWithoutQuery, 'assets/') !== 0) {
        $fullAssetsWebpPath = __DIR__ . '/assets/' . $webpPath;
        if (file_exists($fullAssetsWebpPath) && filesize($fullAssetsWebpPath) > 0) {
            return url('assets/' . $webpPath) . '?v=' . $v;
        }
    }

    // Eğer mobile istendi ama bulunamadıysa normal webp dene
    if ($variant === 'mobile') {
        return webp_url($path);
    }

    // WebP bulunamadı veya bozuk (0 byte), orijinal dosyayı döndür
    // Orijinal dosya kökte mi? (Sorgu parametresiz kontrol et!)
    $fullOriginalPath = __DIR__ . '/' . $pathWithoutQuery;
    if (file_exists($fullOriginalPath) && filesize($fullOriginalPath) > 0) {
        return url($pathWithoutQuery) . '?v=' . $v;
    }

    // Orijinal dosya assets/ altında mı?
    if (strpos($pathWithoutQuery, 'assets/') !== 0) {
        $fullAssetsOriginalPath = __DIR__ . '/assets/' . $pathWithoutQuery;
        if (file_exists($fullAssetsOriginalPath) && filesize($fullAssetsOriginalPath) > 0) {
            return url('assets/' . $pathWithoutQuery) . '?v=' . $v;
        }
    }

    // Hiçbiri değilse güvenli liman: orijinal temiz yolu dön
    return url($pathWithoutQuery) . '?v=' . $v;
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

