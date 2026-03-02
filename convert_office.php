<?php
$baseDir = dirname(__FILE__);
$officeDir = $baseDir . "/assets/images/office/";
$images = [
    "ofis-1.jpg",
    "office2.jpg",
    "office3.jpg",
    "office4.jpg"
];

echo "<h1>Ofis Görselleri Dönüşüm Sistemi</h1>";

if (!function_exists('imagewebp')) {
    die("<p style='color:red'>HATA: imagewebp() fonksiyonu sunucuda aktif değil.</p>");
}

foreach ($images as $imgName) {
    $source = $officeDir . $imgName;
    $dest = $officeDir . pathinfo($imgName, PATHINFO_FILENAME) . ".webp";

    echo "<p>İşleniyor: $imgName...</p>";

    if (!file_exists($source)) {
        echo "<p style='color:orange'>UYARI: Kaynak dosya bulunamadı: $source</p>";
        continue;
    }

    $img = imagecreatefromjpeg($source);
    if ($img) {
        if (imagewebp($img, $dest, 80)) {
            echo "<p style='color:green'>BAŞARILI: $imgName WebP olarak kaydedildi.</p>";
            echo "<ul>";
            echo "<li>Kaynak: " . round(filesize($source) / 1024, 2) . " KB</li>";
            echo "<li>WebP: " . round(filesize($dest) / 1024, 2) . " KB</li>";
            echo "</ul>";
        } else {
            echo "<p style='color:red'>HATA: $imgName kaydedilemedi. İzinleri kontrol edin.</p>";
        }
        imagedestroy($img);
    } else {
        echo "<p style='color:red'>HATA: $imgName yüklenemedi.</p>";
    }
}
?>