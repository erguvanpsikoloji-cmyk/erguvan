<?php
/**
 * Mevcut sertifika görsellerini optimize et
 * Bu script bir kez çalıştırılmalıdır
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../database/db.php';

// Giriş kontrolü
requireLogin();

$db = getDB();

// Sertifikaları getir
$stmt = $db->query("SELECT id, image FROM certificates");
$certificates = $stmt->fetchAll();

$optimized = 0;
$errors = 0;

foreach ($certificates as $cert) {
    $imagePath = $cert['image'];
    
    // URL'den dosya yoluna çevir
    $imagePath = str_replace(BASE_URL . '/assets/images/', '', $imagePath);
    $imagePath = str_replace('assets/images/', '', $imagePath);
    $fullPath = __DIR__ . '/../assets/images/' . $imagePath;
    
    if (!file_exists($fullPath)) {
        $errors++;
        continue;
    }
    
    // Dosya zaten WebP ise ve optimize edilmişse atla
    if (pathinfo($fullPath, PATHINFO_EXTENSION) === 'webp') {
        $imageInfo = getimagesize($fullPath);
        if ($imageInfo && $imageInfo[0] <= 800 && $imageInfo[1] <= 600) {
            continue; // Zaten optimize edilmiş
        }
    }
    
    // GD extension kontrolü
    if (!extension_loaded('gd')) {
        echo "GD extension yüklü değil. Görseller optimize edilemedi.<br>";
        break;
    }
    
    // MIME type'ı al
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fullPath);
    finfo_close($finfo);
    
    // Kaynak görseli yükle
    $sourceImage = null;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($fullPath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($fullPath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($fullPath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($fullPath);
            break;
        default:
            $errors++;
            continue 2;
    }
    
    if (!$sourceImage) {
        $errors++;
        continue;
    }
    
    // Orijinal boyutlar
    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);
    
    // Maksimum boyutlar
    $maxWidth = 800;
    $maxHeight = 600;
    
    // Yeni boyutları hesapla
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = (int)($originalWidth * $ratio);
    $newHeight = (int)($originalHeight * $ratio);
    
    // Eğer görsel zaten küçükse, sadece WebP'ye çevir
    if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    }
    
    // Yeni görsel oluştur
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // PNG ve GIF için şeffaflığı koru
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefill($newImage, 0, 0, $transparent);
    }
    
    // Görseli resize et
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Yeni dosya adı (WebP)
    $newFileName = pathinfo($imagePath, PATHINFO_FILENAME) . '.webp';
    $newFilePath = __DIR__ . '/../assets/images/certificates/' . $newFileName;
    
    // WebP formatında kaydet
    if (imagewebp($newImage, $newFilePath, 85)) {
        // Veritabanını güncelle
        $newUrl = asset_url('images/certificates/' . $newFileName);
        $stmt = $db->prepare("UPDATE certificates SET image = ? WHERE id = ?");
        $stmt->execute([$newUrl, $cert['id']]);
        
        // Eski dosyayı sil (yeni dosya farklı ise)
        if ($fullPath !== $newFilePath && file_exists($fullPath)) {
            @unlink($fullPath);
        }
        
        $optimized++;
    } else {
        $errors++;
    }
    
    // Belleği temizle
    imagedestroy($sourceImage);
    imagedestroy($newImage);
}

echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Görsel Optimizasyonu</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Görsel Optimizasyonu Tamamlandı</h1>
    <p class='success'>Optimize edilen görsel sayısı: $optimized</p>
    <p class='error'>Hata sayısı: $errors</p>
    <p><a href='" . admin_url('pages/certificates.php') . "'>Sertifikalara Dön</a></p>
</body>
</html>";
?>
