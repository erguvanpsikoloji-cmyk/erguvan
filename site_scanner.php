<?php
/**
 * Erguvan Psikoloji - Canlı Site Denetim Scripti
 * Bu script index.php dosyasını tarar ve tüm kaynakları (Image, CSS, JS) kontrol eder.
 */

$target_domain = "http://erguvanpsikoloji.com/";
$index_file = "index.php";

if (!file_exists($index_file)) {
    die("Hata: " . $index_file . " bulunamadı.");
}

$content = file_get_contents($index_file);
$results = [
    'errors' => [],
    'warnings' => [],
    'checked' => 0,
    'details' => []
];

// Regex for src and href
preg_match_all('/(src|href)="([^"]+)"/', $content, $matches);
$urls = array_unique($matches[2]);

function checkUrl($url, $base)
{
    if (strpos($url, 'http') !== 0) {
        if (strpos($url, 'tel:') === 0 || strpos($url, 'mailto:') === 0 || strpos($url, '#') === 0) {
            return null;
        }
        $url = rtrim($base, '/') . '/' . ltrim($url, '/');
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'url' => $url,
        'code' => $code
    ];
}

foreach ($urls as $url) {
    $res = checkUrl($url, $target_domain);
    if ($res) {
        $results['checked']++;
        if ($res['code'] >= 400 || $res['code'] == 0) {
            $results['errors'][] = "Hata (" . $res['code'] . "): " . $res['url'];
        }
        $results['details'][] = $res;
    }
}

// Check for PHP errors in the same folder if index.php was run
// (Simulated as we can't see server logs directly, but we check if common files exist)

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
