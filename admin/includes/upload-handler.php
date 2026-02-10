<?php
/**
 * Görsel yükleme handler'ı
 */

require_once __DIR__ . '/file_utils.php';

function handleImageUpload($file, $folder = 'sliders')
{
    try {
        // Hata kontrolü
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errCode = $file['error'] ?? 'N/A';
            return ['success' => false, 'message' => "Dosya yüklenirken bir hata oluştu (Kod: $errCode)."];
        }

        // Dosya boyutu kontrolü (Sunucu limitine göre dinamik)
        $maxUploadSize = return_bytes(ini_get('upload_max_filesize'));
        $maxPostSize = return_bytes(ini_get('post_max_size'));
        $serverLimit = min($maxUploadSize, $maxPostSize);

        if ($file['size'] > $serverLimit) {
            return ['success' => false, 'message' => 'Dosya boyutu sunucu limitini aşıyor (' . formatSizeUnits($serverLimit) . ').'];
        }

        // İzin verilen dosya tipleri
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => "Geçersiz dosya formatı ($mimeType). Sadece JPG, PNG, GIF ve WebP desteklenir."];
        }

        // Upload dizini
        $uploadDir = __DIR__ . '/../../assets/images/' . $folder . '/';

        // Dizin yoksa oluştur
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => "Hedef klasör oluşturulamadı: $uploadDir"];
            }
        }

        // Benzersiz dosya adı oluştur (her zaman WebP formatında)
        $fileName = uniqid('img_', true) . '.webp';
        $filePath = $uploadDir . $fileName;

        // Görseli yükle ve optimize et
        if (resizeAndOptimizeImage($file['tmp_name'], $filePath, $mimeType, $folder)) {
            // URL'i oluştur
            $url = asset_url('images/' . $folder . '/' . $fileName);
            return ['success' => true, 'url' => $url, 'message' => 'Görsel başarıyla yüklendi ve optimize edildi.'];
        } else {
            return ['success' => false, 'message' => 'Görsel işlenirken bir hata oluştu (resizeAndOptimizeImage başarısız).'];
        }
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Kritik Hata: ' . $e->getMessage()];
    }
}

/**
 * Görseli resize ve optimize et
 */
function resizeAndOptimizeImage($sourcePath, $destinationPath, $mimeType, $folder)
{
    // GD extension kontrolü
    if (!extension_loaded('gd')) {
        // GD yoksa, dosyayı olduğu gibi kopyala
        return copy($sourcePath, $destinationPath);
    }

    // Maksimum boyutlar (folder'a göre)
    $maxDimensions = [
        'certificates' => ['width' => 800, 'height' => 600],
        'sliders' => ['width' => 1200, 'height' => 800],
        'office' => ['width' => 1200, 'height' => 800],
        'blog' => ['width' => 1200, 'height' => 800],
        'default' => ['width' => 1920, 'height' => 1080]
    ];

    $maxWidth = $maxDimensions[$folder]['width'] ?? $maxDimensions['default']['width'];
    $maxHeight = $maxDimensions[$folder]['height'] ?? $maxDimensions['default']['height'];

    // Kaynak görseli yükle
    $sourceImage = null;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    // Orijinal boyutlar
    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);

    // Yeni boyutları hesapla (aspect ratio korunarak)
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = (int) ($originalWidth * $ratio);
    $newHeight = (int) ($originalHeight * $ratio);

    // Eğer görsel zaten küçükse, resize etme
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

    // WebP formatında kaydet (kalite: 85)
    $result = imagewebp($newImage, $destinationPath, 85);

    // Belleği temizle
    imagedestroy($sourceImage);
    imagedestroy($newImage);

    // Ek kontrol: Dosya yazıldı mı ve boş değil mi?
    if ($result && file_exists($destinationPath) && filesize($destinationPath) > 0) {
        return true;
    } else {
        // Hatalı veya boş dosyayı sil
        if (file_exists($destinationPath)) {
            @unlink($destinationPath);
        }
        return false;
    }
}

