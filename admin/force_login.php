<?php
/**
 * ACİL DURUM GİRİŞ DOSYASI
 * Bu dosya güvenlik kontrollerini atlayarak sizi YÖNETİCİ olarak içeri sokar.
 * KULLANDIKTAN SONRA LÜTFEN SİLİN!
 */

// Oturumu başlat
if (session_status() === PHP_SESSION_NONE) {
    // Sunucu bazlı oturum sorunlarını gidermek için özel bir klasör deneyelim
    $custom_session_dir = __DIR__ . '/../sessions';
    if (!is_dir($custom_session_dir)) {
        mkdir($custom_session_dir, 0755, true);
    }
    if (is_dir($custom_session_dir) && is_writable($custom_session_dir)) {
        session_save_path($custom_session_dir);
    }
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

try {
    $db = getDB();
    // İlk admin kullanıcısını bul
    $stmt = $db->query("SELECT * FROM admin_users LIMIT 1");
    $user = $stmt->fetch();

    if ($user) {
        // Oturum değişkenlerini manuel ata
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        echo "<h2 style='color:green'>BAŞARILI! Yurttaş, içeri alınıyorsun...</h2>";
        echo "<p>3 saniye içinde Dashboard'a yönlendirileceksiniz.</p>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<h2 style='color:red'>HATA: Veritabanında admin kullanıcısı bulunamadı!</h2>";
    }
} catch (Exception $e) {
    echo "<h2 style='color:red'>HATA: " . $e->getMessage() . "</h2>";
}
?>