<?php
$baseDir = dirname(__FILE__);
$source = $baseDir . "/assets/images/hero-psikolojik-destek.jpg";
$dest = $baseDir . "/assets/images/hero-psikolojik-destek.webp";

echo "<h1>Görsel Dönüşüm Sistemi</h1>";
echo "<p>Aranıyor: $source</p>";

if (!function_exists('imagewebp')) {
    die("<p style='color:red'>HATA: imagewebp() fonksiyonu sunucuda aktif değil.</p>");
}

if (!file_exists($source)) {
    die("<p style='color:red'>HATA: Kaynak dosya bulunamadı: $source</p>");
}

$img = imagecreatefromjpeg($source);
if ($img) {
    if (imagewebp($img, $dest, 80)) {
        echo "<p style='color:green'>BAŞARILI: Görsel WebP olarak kaydedildi.</p>";
        echo "<ul>";
        echo "<li>Kaynak: " . round(filesize($source) / 1024, 2) . " KB</li>";
        echo "<li>WebP: " . round(filesize($dest) / 1024, 2) . " KB</li>";
        echo "</ul>";
    } else {
        echo "<p style='color:red'>HATA: WebP kaydedilemedi. İzinleri kontrol edin.</p>";
    }
    imagedestroy($img);
} else {
    echo "<p style='color:red'>HATA: Kaynak görsel yüklenemedi.</p>";
}
?>