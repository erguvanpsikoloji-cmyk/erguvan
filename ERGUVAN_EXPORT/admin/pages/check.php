<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. Başladı<br>";
require_once __DIR__ . '/../../config.php';
echo "2. config.php yüklendi (BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'TANIMSIZ') . ")<br>";

require_once __DIR__ . '/../includes/auth.php';
echo "3. auth.php yüklendi<br>";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "4. Session başlatıldı<br>";

require_once __DIR__ . '/../includes/csrf.php';
echo "5. csrf.php yüklendi<br>";

require_once __DIR__ . '/../../database/db.php';
echo "6. db.php yüklendi<br>";

try {
    $db = getDB();
    echo "7. Veritabanı bağlantısı denendi (Tip: " . get_class($db) . ")<br>";
} catch (Exception $e) {
    echo "7. Veritabanı HATASI: " . $e->getMessage() . "<br>";
}

require_once __DIR__ . '/../includes/upload-handler.php';
echo "8. upload-handler.php yüklendi<br>";

require_once __DIR__ . '/../includes/header.php';
echo "9. header.php yüklendi<br>";

echo "10. Tamamlandı. Eğer burayı görüyorsanız temel yapı çalışıyor demektir.";
?>