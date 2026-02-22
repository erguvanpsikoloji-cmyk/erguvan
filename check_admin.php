<?php
/**
 * ACİL DURUM GİRİŞ VE SİSTEM ONARIM DOSYASI
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();

    // İlk admin kullanıcısını al
    $stmt = $db->query("SELECT * FROM admin_users LIMIT 1");
    $user = $stmt->fetch();

    if ($user) {
        // Oturumu başlat
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Giriş Başarılı</title>";
        echo "<style>body{font-family:sans-serif;text-align:center;padding:50px;background:#fdf2f8;} .box{background:white;padding:40px;border-radius:15px;max-width:400px;margin:0 auto;box-shadow:0 10px 25px rgba(219,39,119,0.1);}</style></head><body>";
        echo "<div class='box'><h1 style='color:#db2777'>🌸 Giriş Başarılı</h1>";
        echo "<p>Hoş geldiniz, <strong>" . htmlspecialchars($user['username']) . "</strong></p>";
        echo "<p>Panelinize yönlendiriliyorsunuz...</p>";
        echo "<script>setTimeout(function(){ window.location.href = 'admin/index.php'; }, 1500);</script>";
        echo "<a href='admin/index.php' style='display:inline-block;background:#db2777;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-top:20px;'>Hemen Git</a></div></body></html>";
    } else {
        echo "Hata: Admin kullanıcısı bulunamadı.";
    }
} catch (Exception $e) {
    echo "Veritabanı Hatası: " . $e->getMessage();
}
?>