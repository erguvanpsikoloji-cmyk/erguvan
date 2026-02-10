<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$targetDir = __DIR__ . '/../../assets/images/blog/';
$realPath = realpath($targetDir);

echo "<h2>Dosya Yazma İzni Testi</h2>";
echo "<strong>Hedef Dizin:</strong> " . $targetDir . "<br>";
echo "<strong>Gerçek Yol:</strong> " . ($realPath ? $realPath : "Bulunamadı") . "<br>";

if (!is_dir($targetDir)) {
    echo "❌ Klasör yok. Oluşturulmaya çalışılıyor...<br>";
    if (mkdir($targetDir, 0755, true)) {
        echo "✅ Klasör oluşturuldu.<br>";
    } else {
        echo "❌ Klasör OLUŞTURULAMADI. Hata: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "✅ Klasör mevcut.<br>";
}

if (is_writable($targetDir)) {
    echo "✅ Klasör YAZILABİLİR.<br>";

    $testFile = $targetDir . 'test_write.txt';
    if (file_put_contents($testFile, 'Test content ' . date('Y-m-d H:i:s'))) {
        echo "✅ Test dosyası başarıyla yazıldı (" . $testFile . ").<br>";
        unlink($testFile); // Temizle
        echo "✅ Test dosyası silindi.<br>";
    } else {
        echo "❌ Test dosyası YAZILAMADI.<br>";
    }
} else {
    echo "❌ Klasör YAZILAMAZ (Permission Denied).<br>";
    echo "Mevcut İzinler: " . substr(sprintf('%o', fileperms($targetDir)), -4) . "<br>";
    echo "Lütfen FTP üzerinden bu klasöre 775 veya 777 izni verin.<br>";
}
?>