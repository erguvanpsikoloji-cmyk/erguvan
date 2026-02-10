<?php
/**
 * ERGUVAN PSİKOLOJİ - AGRESİF GÖRSEL OPTİMİZASYON ARACI
 * 1. Resimleri sıkıştırır (Daha düşük WebP kalitesi)
 * 2. Mobil versiyonları oluşturur (_mobile.webp)
 * 3. Logo ve kritik resimleri optimize eder
 */

set_time_limit(300);
$quality = 65; // Agresif sıkıştırma

function optimize_directory($dir, $quality)
{
    if (!is_dir($dir))
        return;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..')
            continue;
        $path = $dir . '/' . $item;

        if (is_dir($path)) {
            optimize_directory($path, $quality);
        } else {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'webp') {
                // Sadece normal webp'leri optimize et, mobile olanları atla (sonsuz döngü olmasın)
                if (strpos($item, '_mobile.webp') !== false)
                    continue;

                $img = @imagecreatefromwebp($path);
                if ($img) {
                    $width = imagesx($img);
                    $height = imagesy($img);

                    // 1. Orijinali daha fazla sıkıştır (Güvenli yazma)
                    $tempPath = $path . '.tmp';
                    if (imagewebp($img, $tempPath, $quality)) {
                        if (file_exists($tempPath) && filesize($tempPath) > 0) {
                            rename($tempPath, $path);
                            echo "Optimize edildi: $path<br>";
                        } else {
                            @unlink($tempPath);
                            echo "Hata: Optimizasyon boş dosya üretti, atlanıyor: $path<br>";
                        }
                    }

                    // 2. Mobil versiyonu oluştur (Maks 640px)
                    $mobilePath = str_replace('.webp', '_mobile.webp', $path);
                    if ($width > 640) {
                        $newWidth = 640;
                        $newHeight = floor($height * ($newWidth / $width));
                        $mobileImg = imagecreatetruecolor($newWidth, $newHeight);
                        imagealphablending($mobileImg, false);
                        imagesavealpha($mobileImg, true);
                        imagecopyresampled($mobileImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        $tempMobilePath = $mobilePath . '.tmp';
                        if (imagewebp($mobileImg, $tempMobilePath, $quality)) {
                            if (file_exists($tempMobilePath) && filesize($tempMobilePath) > 0) {
                                rename($tempMobilePath, $mobilePath);
                                echo "Mobil versiyon oluşturuldu: $mobilePath<br>";
                            } else {
                                @unlink($tempMobilePath);
                            }
                        }
                        imagedestroy($mobileImg);
                    } else {
                        // Eğer zaten küçükse ve mobil versiyon yoksa, orijinali kopyala
                        if (!file_exists($mobilePath) || filesize($mobilePath) == 0) {
                            copy($path, $mobilePath);
                        }
                    }

                    imagedestroy($img);
                }
            } elseif ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
                // WebP değilse dönüştür veya sadece optimize et
                // Şimdilik sadece WebP odaklıyız çünkü site WebP kullanıyor
            }
        }
    }
}

echo "<h2>Görsel Optimizasyonu Başlatıldı...</h2>";
$baseDir = __DIR__ . '/assets/images';
optimize_directory($baseDir, $quality);
echo "<h3>Tüm görseller başarıyla optimize edildi!</h3>";
?>